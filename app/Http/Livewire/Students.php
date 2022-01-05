<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Models\Level;
use App\Models\Hostel;
use App\Models\Stream;
use App\Models\Student;
use Livewire\Component;
use App\Models\Guardian;
use App\Models\LevelUnit;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Exports\StudentsExport;
use App\Imports\StudentsImport;
use Illuminate\Validation\Rule;
use App\Rules\MustBeKenyanPhone;
use App\Settings\SystemSettings;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\Paginator;

class Students extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['addStudentGuardiansFeedback'];

    public $studentId;

    public $adm_no;
    public $name;
    public $upi;
    public $kcpe_marks;
    public $kcpe_grade;
    public $gender;
    public $dob;
    public $admission_level_id;
    public $level_id;
    public $hostel_id;
    public $stream_id;
    public $description;

    public $student = array(
        'adm_no' => null,
        'name' => null,
        'upi' => null,
        'kcpe_marks' => null,
        'kcpe_grade' => null,
        'gender' => null,
        'dob' => null,
        'admission_level_id' => null,
        'level_id' => null,
        'hostel_id' => null,
        'stream_id' => null,
        'description' => null,
    );

    public $guardian = array(
        'name' => null,
        'email' => null,
        'phone' => null,
        'location' => null,
        'profession' => null
    );

    public $studentsFile;

    public function render()
    {
        return view('livewire.students',[
            'students' => $this->getPaginatedStudents(),
            'levels' => $this->getAllLevels(),
            'streams' => $this->getAllStreams(),
            'genderOptions' => User::genderOptions(),
            'kcpeGradeOptions' => Student::kcpeGradeOptions(),
            'hostels' => $this->getAllHostels()
        ]);
    }

    /**
     * Get paginated students from the database
     * 
     * @return Paginator
     */
    public function getPaginatedStudents()
    {
        return Student::with(['level','levelUnit'])
            ->latest()->paginate(24)
            ->withQueryString();
    }

    /**
     * Get all levels from the database
     * 
     * @return Collection
     */
    public function getAllLevels()
    {
        return Level::all(['id', 'name']);
    }

    /**
     * Get all streams from the database
     * 
     * @return Collection
     */
    public function getAllStreams()
    {
        return Stream::all(['id', 'name']);
    }

    /**
     * Get all hostels from the database
     * 
     * @return Collection
     * 
     */
    public function getAllHostels()
    {
        return Hostel::all(['id', 'name']);
    }

    /**
     * Validation of the original student fields
     * 
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['bail', 'required', 'string'],
            'adm_no' => ['bail', 'required', Rule::unique('students')->ignore($this->studentId)],
            'upi' => ['bail', 'nullable'],
            'gender' => ['bail', Rule::in(User::genderOptions())],
            'dob' => ['bail', 'string'],
            'admission_level_id' => ['bail', 'nullable', 'integer'],
            'level_id' => ['bail', 'nullable', 'integer'],
            'hostel_id' => ['bail', 'nullable', 'integer'],
            'stream_id' => ['bail', 'nullable', 'integer'],
            'description' => ['bail', 'nullable'],
            'kcpe_grade' => ['bail', 'nullable', Rule::in(Student::kcpeGradeOptions())],
            'kcpe_marks' => ['bail', 'nullable', 'integer', 'between:1,500']
        ];
    }

    /**
     * Hook to make sure that guardian contact is appropriate
     * 
     * @param mixed $value
     */
    public function updatedGuardian($value)
    {
        if (isset($this->guardian['phone']) && isset($value['phone'])) {
            $this->guardian['phone'] = Str::start($value['phone'], '254');
        }
    }

    /**
     * Persist a new student to the database
     */
    public function addStudent()
    {
        $data = $this->validate();

        $data = array_filter($data, fn($value, $key) => !empty($value), ARRAY_FILTER_USE_BOTH);

        try {

            $systemSettings = app(SystemSettings::class);

            $access = Gate::inspect('create', Student::class);

            if($access->allowed()){

                if ($systemSettings->school_has_streams) {
                    // Based on level and stream, get the level_unit_id and also persists
                    $data['level_unit_id'] = LevelUnit::where([
                        'level_id' => $data['admission_level_id'],
                        'stream_id' => $data['stream_id']
                    ])->firstOrFail()->id;
                }
                
                $student = Student::create($data);

                if($student){
    
                    $this->reset();
    
                    $this->resetPage();
    
                    session()->flash('status', 'Student successfully added');
    
                    $this->emit('hide-upsert-student-modal');
                }

            }else{

                session()->flash('error', $access->message());
    
                $this->emit('hide-upsert-student-modal');

            }

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', 'A db/error occurred');

            $this->emit('hide-upsert-student-modal');
        }
    }

    /**
     * Launch a modal for updating student
     * 
     * @param Student $student
     */
    public function editStudent(Student $student)
    {
        $this->studentId = $student->id;

        $this->adm_no = $student->adm_no;
        $this->upi = $student->upi;
        $this->name = $student->name;
        $this->dob = $student->dob->format('Y-m-d');
        $this->gender = $student->gender;
        $this->kcpe_marks = $student->kcpe_marks;
        $this->kcpe_grade = $student->kcpe_grade;
        $this->stream_id = $student->stream_id;
        $this->level_id = $student->level_id;
        $this->hostel_id = $student->hostel_id;
        $this->description = $student->description;

        $this->emit('show-upsert-student-modal');
    }

    /**
     * Updated a database student record
     */
    public function updateStudent()
    {
        $data = $this->validate();

        $data = array_filter($data, fn($value, $key) => !empty($value), ARRAY_FILTER_USE_BOTH);

        try {

            /** @var Student */
            $student = Student::findOrFail($this->studentId);

            // Based on level and stream, get the level_unit_id and also persists
            $data['level_unit_id'] = LevelUnit::where([
                'level_id' => $data['level_id'],
                'stream_id' => $data['stream_id']
            ])->firstOrFail()->id;

            $access = Gate::inspect('update', $student);

            if($access->allowed()){
                
                if($student->update($data)){
    
                    $this->reset();
    
                    $this->resetValidation();
    
                    session()->flash('status', 'Student has been successfully updated');
    
                    $this->emit('hide-upsert-student-modal');
    
                }

            }else{

                session()->flash('error', $access->message());
    
                $this->emit('hide-upsert-student-modal');

            }


        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__,
                'student-id' => $this->studentId
            ]);

            session()->flash('error', 'Fatal error occurred while udating student, perhaps the result class is missing');

            $this->emit('hide-upsert-student-modal');
        }
        
    }

    /**
     * Add a new student
     */
    public function newAddStudent()
    {
        
        $data = $this->validate([
            'student.name' => ['bail', 'required', 'string'],
            'student.adm_no' => ['bail', 'required', Rule::unique('students', 'adm_no')],
            'student.upi' => ['bail', 'nullable'],
            'student.gender' => ['bail', Rule::in(User::genderOptions())],
            'student.dob' => ['bail', 'string'],
            'student.admission_level_id' => ['bail', 'nullable', 'integer'],
            'student.level_id' => ['bail', 'nullable', 'integer'],
            'student.hostel_id' => ['bail', 'nullable', 'integer'],
            'student.stream_id' => ['bail', 'nullable', 'integer'],
            'student.description' => ['bail', 'nullable'],
            'student.kcpe_grade' => ['bail', 'nullable', Rule::in(Student::kcpeGradeOptions())],
            'student.kcpe_marks' => ['bail', 'nullable', 'integer', 'between:1,500'],
            'guardian.name' => ['bail', 'required', 'string'],
            'guardian.email' => ['bail', 'required', 'string', 'email', Rule::unique('users', 'email')],
            'guardian.phone' => ['bail', 'required', Rule::unique('users', 'phone'), new MustBeKenyanPhone()],
            'guardian.profession' => ['bail', 'nullable'],
            'guardian.location' => ['bail', 'nullable']
        ]);

        try {

            $dataStudent = array_filter($data['student'], fn($value, $key) => !empty($value), ARRAY_FILTER_USE_BOTH);

            $systemSettings = app(SystemSettings::class);
    
            $access = Gate::inspect('create', Student::class);
    
            if($access->allowed()){
    
                DB::beginTransaction();
    
                if ($systemSettings->school_has_streams) {
                    // Based on level and stream, get the level_unit_id and also persists
                    $dataStudent['level_unit_id'] = LevelUnit::where([
                        'level_id' => $dataStudent['admission_level_id'],
                        'stream_id' => $dataStudent['stream_id']
                    ])->firstOrFail()->id;
                }
                
                /** @var Student */
                $student = Student::create($dataStudent);
    
                /** @var Guardian */
                $guardian = Guardian::create($data['guardian']);

                if($guardian) $guardian->auth()->create(array_merge($data['guardian'], ['password' => Hash::make('password')]));
    
                if($student && $guardian){

                    $student->guardians()->attach($guardian);
    
                    DB::commit();
    
                    $this->reset(['student', 'guardian']);
        
                    $this->resetPage();
        
                    session()->flash('status', 'The student and guardian have successfully been added');
        
                    $this->emit('hide-add-student-modal');
                }
    
            }else{
    
                session()->flash('error', $access->message());
    
                $this->emit('hide-add-student-modal');
    
            }

        } catch (\Exception $exception) {

            DB::rollBack();

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', 'Fatal error occurred while adding student');

            $this->emit('hide-add-student-modal');
            
        }
    }

    /**
     * Show a modal for deleting a student
     * 
     * @param Student $student
     */
    public function showDeleteStudentModal(Student $student)
    {
        $this->studentId = $student->id;

        $this->name = $student->name;

        $this->emit('show-delete-student-modal');
    }

    /**
     * Delete a student record from the database
     */
    public function deleteStudent()
    {
        
        try {
            
            /** @var Student */
            $student = Student::findOrFail($this->studentId);

            $access = Gate::inspect('delete', $student);

            if($access->allowed()){

                if($student->delete()){
    
                    $this->reset();
    
                    $this->resetPage();
    
                    session()->flash('status', 'Student has been successfully deleted');
    
                    $this->emit('hide-delete-student-modal');
                }

            }else{

                session()->flash('error', $access->message());
    
                $this->emit('hide-delete-student-modal');
            }

        } catch (\Exception $exception) {
         
            Log::error($exception->getMessage(), [
                'action' => __METHOD__,
                'student-id' => $this->studentId
            ]);

            session()->flash('error', 'A fatal error occurred while trying to delete student');

            $this->emit('hide-delete-student-modal');
        }
    }

    /**
     * Attach quardians to student
     * 
     * @param Student $student
     */
    public function showAddStudentGuardiansModal(Student $student)
    {
        $this->emitTo('add-student-guardians', 'showAddStudentGuardiansModal', $student);
    }

    public function addStudentGuardiansFeedback(array $payload)
    {
        session()->flash($payload['type'], $payload['message']);

        $this->emit('hide-add-student-guardians-modal');
        
    }

    public function downloadSpreadSheet()
    {
        return Excel::download(new StudentsExport, 'students.xlsx');
    }

    public function downloadUploadStudentsExcelFile()
    {
        // $cols = [["Adm. No.", "Name", "KCPE Marks", "KCPE Grade", "Gender (Male, Female, Other)", "DOB (YYYY-MM-DD)", "class (1B)"]];
        $cols = [["ADMNO","NAME","KCPEMARKS","KCPEGRADE","GENDER (Male, Female, Other)","DOB (YYYY-MM-DD)","CLASS (1B)"]];
        
        $headers = collect($cols);

        return $headers->downloadExcel("new-student.xlsx");
        
    }

    public function importStudents()
    {
        $data = $this->validate(['studentsFile' => ['file', 'mimes:xlsx,csv,ods,xlsm,xltx,xltm,xls,xlt,xml']]);

        /** @var UploadedFile */
        $file = $data['studentsFile'];

        try {
            
            Excel::import(new StudentsImport, $file);
    
            session()->flash('status', 'Students Successfully imported');
            
            $this->emit('hide-import-student-spreadsheet-modal');

        } catch (\Exception $exception) {
         
            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', 'A fatal error occurred while trying to import students');
            
            $this->emit('hide-import-student-spreadsheet-modal');
            
        }

    }
}
