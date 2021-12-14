<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Models\Subject;
use App\Models\Teacher;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class Teachers extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $teacherId;
    public $userId;

    public $name;
    public $email;
    public $employer;
    public $tsc_number;

    public $selectedSubjects = [];

    public function render()
    {
        return view('livewire.teachers', [
            'teachers' => $this->getPaginatedTeachers(),
            'employers' => Teacher::employerOptions(),
            'subjects' => $this->getAllSubjects()
        ]);
    }

    public function getPaginatedTeachers()
    {
        return Teacher::with(['responsibilities'])->latest()->paginate(24);
    }

    public function getAllSubjects()
    {
        return Subject::all(['id', 'name']);
    }

    public function rules()
    {
        return [
            'name' => ['bail', 'required', 'string'],
            'email' => ['bail', 'required', 'string', 'email', Rule::unique('users')->ignore($this->userId)],
            'employer' => ['bail', 'required', Rule::in(Teacher::employerOptions())],
            'tsc_number' => ['bail', 'nullable', Rule::unique('teachers')->ignore($this->teacherId)],
            'selectedSubjects' => ['bail', 'nullable', 'array']
        ];
    }

    public function addTeacher()
    {
        $data = $this->validate();

        try {

            DB::beginTransaction();

            /** @var Teacher */
            $teacher = Teacher::create($data);

            if($teacher){

                /** @var User */
                $user = $teacher->auth()->create(array_merge($data, [
                    'password' => Hash::make('password')
                ]));

                //$user->sendEmailVerificationNotification();

                if($user){

                    if(isset($data['selectedSubjects']) && !is_null($data['selectedSubjects'])){

                        $payload = array_filter($data['selectedSubjects'], function($value, $key){
                            return $value == 'true';
                        }, ARRAY_FILTER_USE_BOTH);

                        $teacher->subjects()->sync(array_keys($payload));

                    }

                    DB::commit();

                    $this->reset(['name', 'email', 'employer', 'tsc_number', 'selectedSubjects']);

                    $this->resetPage();

                    session()->flash('status', 'Teacher successfully created');

                    $this->emit('hide-upsert-teacher-modal');
                }

            }
            
        } catch (\Exception $exception) {

            DB::rollBack();

            Log::error($exception->getMessage(), [
                'teacher-id' => $this->teacherId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

            session()->flash('error', 'A fatal error occurred check the logs');

        }
        
    }

    public function editTeacher(Teacher $teacher)
    {

        $this->teacherId = $teacher->id;
        $this->userId = $teacher->auth->id;

        $this->name = $teacher->auth->name;
        $this->email = $teacher->auth->email;

        $this->employer = $teacher->employer;
        $this->tsc_number = $teacher->tsc_number;

        foreach ($teacher->subjects->pluck('id')->toArray() as $subject) {
            $this->selectedSubjects[$subject] = 'true';
        }

        $this->emit('show-upsert-teacher-modal');
        
    }

    public function updateTeacher()
    {
        $data = $this->validate();

        try {

            /** @var Teacher */
            $teacher = Teacher::findOrFail($this->teacherId);

            DB::beginTransaction();

            if($teacher->update($data)){

                if($teacher->auth->update($data)){

                    if(isset($data['selectedSubjects']) && !is_null($data['selectedSubjects'])){

                        $payload = array_filter($data['selectedSubjects'], function($value, $key){
                            return $value == 'true';
                        }, ARRAY_FILTER_USE_BOTH);

                        $teacher->subjects()->sync(array_keys($payload));

                    }

                    DB::commit();

                    $this->reset(['teacherId', 'userId', 'name', 'email', 'employer', 'tsc_number', 'selectedSubjects']);

                    $this->resetPage();

                    session()->flash('status', 'Teacher Successfully Updated');

                    $this->emit('hide-upsert-teacher-modal');

                }
            }

        } catch (\Exception $exception) {

            DB::rollBack();

            Log::error($exception->getMessage(), [
                'teacher-id' => $this->teacherId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

            session()->flash('error', 'A fatal error occurred check the logs');

            $this->emit('hide-upsert-teacher-modal');
            
        }
        
    }

    public function showDeleteTeacherModal(Teacher $teacher)
    {

        $this->teacherId = $teacher->id;

        $this->name = $teacher->auth->name;

        $this->emit('show-delete-teacher-modal');
        
    }

    public function deleteTeacher()
    {
        try {

            /** @var Teacher */
            $teacher = Teacher::findOrFail($this->teacherId);

            DB::beginTransaction();

            if($teacher->auth) $teacher->auth->delete();

            if($teacher->delete()){

                DB::commit();

                $this->reset(['teacherId', 'name']);

                $this->resetPage();

                session()->flash('status', 'Teacher has been succefully deleted');

                $this->emit('hide-delete-teacher-modal');
            }
            
        } catch (\Exception $exception) {

            DB::rollBack();

            Log::error($exception->getMessage(), [
                'teacher-id' => $this->teacherId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

            session()->flash('error', 'A fatal error occurred check the logs');

            $this->emit('hide-delete-teacher-modal');
            
        }
        
    }
}
