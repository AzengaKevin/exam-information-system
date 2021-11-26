<?php

namespace App\Http\Livewire;

use App\Models\Level;
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
            'genderOptions' => User::genderOptions()
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
            'description' => ['bail', 'nullable']
        ];
    }

    public function addStudent()
    {
        $data = $this->validate();

        try {

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
            
        }
    }
}
