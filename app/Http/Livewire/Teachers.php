<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Models\Subject;
use App\Models\Teacher;
use App\Notifications\SendPasswordNotification;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use App\Rules\MustBeKenyanPhone;
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
    public $phone;
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

    /**
     * Ensures that the phone number is prefixed with 254 when entered
     */
    public function updatedPhone($value)
    {
        $this->phone = Str::start($value, "254");
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
            'email' => ['bail', 'nullable', 'string', 'email', Rule::unique('users')->ignore($this->userId)],
            'phone' => ['bail', 'required', Rule::unique('users')->ignore($this->userId), new MustBeKenyanPhone()],
            'employer' => ['bail', 'required', Rule::in(Teacher::employerOptions())],
            'tsc_number' => ['bail', 'nullable', Rule::unique('teachers')->ignore($this->teacherId)],
            'selectedSubjects' => ['bail', 'nullable', 'array']
        ];
    }

    /**
     * Adding a new teacher record in the database
     */
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
                    'password' => Hash::make($password = Str::random(6))
                ]));

                // Sending email verification link to the user
                if(!empty($user->email)) $user->sendEmailVerificationNotification();

                // Send the guardian a password
                $user->notifyNow(new SendPasswordNotification($password));

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

            session()->flash('error', $exception->getMessage());

        }
        
    }

    /**
     * Show the modal for editing the specified teacher
     * 
     * @param Teacher $teacher
     */
    public function editTeacher(Teacher $teacher)
    {

        $this->teacherId = $teacher->id;
        $this->userId = $teacher->auth->id;

        $this->name = $teacher->auth->name;
        $this->email = $teacher->auth->email;
        $this->phone = $teacher->auth->phone;

        $this->employer = $teacher->employer;
        $this->tsc_number = $teacher->tsc_number;

        foreach ($teacher->subjects->pluck('id')->toArray() as $subject) {
            $this->selectedSubjects[$subject] = 'true';
        }

        $this->emit('show-upsert-teacher-modal');
        
    }

    /**
     * Updates the changed details of the current teacher
     */
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
