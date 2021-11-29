<?php

namespace App\Http\Livewire;

use App\Models\Level;
use App\Models\LevelUnit;
use App\Models\Stream;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Students extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $studentId;

    public $adm_no;
    public $name;
    public $upi;
    public $kcpe_marks;
    public $kcpe_grade;
    public $gender;
    public $dob;
    public $admission_level_id;
    public $stream_id;
    public $description;

    public function render()
    {
        return view('livewire.students',[
            'students' => $this->getPaginatedStudents(),
            'levels' => $this->getAllLevels(),
            'streams' => $this->getAllStreams(),
            'genderOptions' => User::genderOptions(),
            'kcpeGradeOptions' => Student::kcpeGradeOptions()
        ]);
    }

    public function getPaginatedStudents()
    {
        return Student::orderBy('adm_no')->paginate(24);
    }

    public function getAllLevels()
    {
        return Level::all(['id', 'name']);
    }

    public function getAllStreams()
    {
        return Stream::all(['id', 'name']);
    }

    public function rules()
    {
        return [
            'name' => ['bail', 'required', 'string'],
            'adm_no' => ['bail', 'required', Rule::unique('students')->ignore($this->studentId)],
            'upi' => ['bail', 'nullable'],
            'gender' => ['bail', Rule::in(User::genderOptions())],
            'dob' => ['bail', 'string'],
            'admission_level_id' => ['bail', 'required', 'integer'],
            'stream_id' => ['bail', 'required', 'integer'],
            'description' => ['bail', 'nullable'],
            'kcpe_grade' => ['bail', 'required', Rule::in(Student::kcpeGradeOptions())],
            'kcpe_marks' => ['bail', 'required', 'integer']
        ];
    }

    public function addStudent()
    {
        $data = $this->validate();

        try {

            // Based on level and stream, get the level_unit_id and also persists
            $data['level_unit_id'] = LevelUnit::firstOrCreate([
                'level_id' => $data['admission_level_id'],
                'stream_id' => $data['stream_id']
            ])->id;

            $student = Student::create($data);

            if($student){

                $this->reset();

                $this->resetPage();

                session()->flash('status', 'Student successfully added');

                $this->emit('hide-upsert-student-modal');
            }

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', 'A fatal error occurred while trying to add student');

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
        $this->admission_level_id = $student->admission_level_id;
        $this->description = $student->description;

        $this->emit('show-upsert-student-modal');
    }

    public function updateStudent()
    {
        $data = $this->validate();

        try {

            /** @var Student */
            $student = Student::findOrFail($this->studentId);

            if($student->update($data)){

                $this->reset();

                $this->resetValidation();

                session()->flash('status', 'Student has been successfully updated');

                $this->emit('hide-upsert-student-modal');

            }


        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__,
                'student-id' => $this->studentId
            ]);

            session()->flash('error', 'Fatal error occurred while udating student');

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

            if($student->delete()){

                $this->reset();

                $this->resetPage();

                session()->flash('status', 'Student has been successfully deleted');

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
}
