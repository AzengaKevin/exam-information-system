<?php

namespace App\Http\Livewire;

use App\Models\User;
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

    public function render()
    {
        return view('livewire.teachers', [
            'teachers' => $this->getPaginatedTeachers(),
            'employers' => Teacher::employerOptions()
        ]);
    }

    public function getPaginatedTeachers()
    {
        return Teacher::latest()->paginate(24);
    }

    public function rules()
    {
        return [
            'name' => ['bail', 'required', 'string'],
            'email' => ['bail', 'required', 'string', 'email', Rule::unique('users')->ignore($this->userId)],
            'employer' => ['bail', 'required', Rule::in(Teacher::employerOptions())],
            'tsc_number' => ['bail', 'nullable', Rule::unique('teachers')->ignore($this->teacherId)]
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

                    DB::commit();

                    $this->reset(['name', 'email', 'employer', 'tsc_number']);

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
}
