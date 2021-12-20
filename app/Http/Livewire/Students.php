<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Models\Level;
use App\Models\Stream;
use App\Models\Student;
use Livewire\Component;
use App\Models\LevelUnit;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Exports\StudentsExport;
use App\Imports\StudentsImport;
use App\Models\Hostel;
use Illuminate\Validation\Rule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;

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

    public function getPaginatedStudents()
    {
        return Student::with(['levelUnit'])->latest()->paginate(24);
    }

    public function getAllLevels()
    {
        return Level::all(['id', 'name']);
    }

    public function getAllStreams()
    {
        return Stream::all(['id', 'name']);
    }

    public function getAllHostels()
    {
        return Hostel::all(['id', 'name']);
    }

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
            'stream_id' => ['bail', 'required', 'integer'],
            'description' => ['bail', 'nullable'],
            'kcpe_grade' => ['bail', 'required', Rule::in(Student::kcpeGradeOptions())],
            'kcpe_marks' => ['bail', 'required', 'integer']
        ];
    }

    public function addStudent()
    {
        $data = $this->validate();

        $data = array_filter($data, fn($value, $key) => !empty($value), ARRAY_FILTER_USE_BOTH);

        try {

            $access = Gate::inspect('create', Student::class);

            if($access->allowed()){

                // Based on level and stream, get the level_unit_id and also persists
                $data['level_unit_id'] = LevelUnit::where([
                    'level_id' => $data['admission_level_id'],
                    'stream_id' => $data['stream_id']
                ])->firstOrFail()->id;

                
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

            session()->flash('error', 'A fatal error occurred while trying to add student, perhaps you have not, generated classes form the levels and streams available');

            $this->emit('hide-upsert-student-modal');
        }
    }

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

    public function showDeleteStudentModal(Student $student)
    {
        $this->studentId = $student->id;

        $this->name = $student->name;

        $this->emit('show-delete-student-modal');
    }

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
