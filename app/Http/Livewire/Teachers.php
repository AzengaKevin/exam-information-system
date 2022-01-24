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
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class Teachers extends Component
{
    use WithPagination, AuthorizesRequests;

    protected $paginationTheme = 'bootstrap';

    public $trashed = false;
    public $subjects;

    public $teacherId;
    public $userId;

    public $name;
    public $email;
    public $phone;
    public $employer;
    public $tsc_number;

    public $selectedSubjects = [];

    /**
     * Lifecycle method, that executes onces when the component is mounting
     * 
     * @param string $trashed
     */
    public function mount(string $trashed = null)
    {
        $this->trashed = boolval($trashed);

        $this->subjects = $this->getAllSubjects();
    }

    /**
     * Lifecycle method that renders the component every time it's state changes
     * 
     * @return View
     */
    public function render()
    {
        return view('livewire.teachers', [
            'teachers' => $this->getPaginatedTeachers(),
            'employers' => Teacher::employerOptions()
        ]);
    }

    /**
     * Ensures that the phone number is prefixed with 254 when entered
     * 
     * @param mixed $value
     */
    public function updatedPhone($value)
    {
        $this->phone = Str::start($value, "254");
    }

    /**
     * Get paginated teachers from the database
     * 
     * @return Collection
     */
    public function getPaginatedTeachers()
    {
        $teacherQuery = Teacher::with(['responsibilities']);

        if ($this->trashed) $teacherQuery->onlyTrashed();
        
        return $teacherQuery->latest()->paginate(24);
    }

    /**
     * Get all subjects from the database
     * 
     * @return Collection
     */
    public function getAllSubjects()
    {
        return Subject::all(['id', 'name']);
    }

    /**
     * Teacher field validation rules
     * 
     * @return array
     */
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

            $this->authorize('create', Teacher::class);
            $this->authorize('create', User::class);

            DB::transaction(function()use($data){

                /** @var Teacher */
                $teacher = Teacher::create($data);

                /** @var User */
                $user = $teacher->auth()->create(array_merge($data, [
                    'password' => Hash::make($password = Str::random(6))
                ]));

                if (App::environment('local')) {

                    Log::debug([
                        'phone' => $user->phone,
                        'password' => $password
                    ]);

                }else{
                    // Sending email verification link to the user
                    if(!empty($user->email)) $user->sendEmailVerificationNotification();
    
                    // Send the guardian a password
                    $user->notifyNow(new SendPasswordNotification($password));
                }


                if(isset($data['selectedSubjects']) && !is_null($data['selectedSubjects'])){

                    $payload = array_filter($data['selectedSubjects'], fn($value, $key) => $value == 'true', ARRAY_FILTER_USE_BOTH);

                    $teacher->subjects()->sync(array_keys($payload));

                }

            });

            $this->reset(['name', 'email', 'phone', 'employer', 'tsc_number', 'selectedSubjects']);

            $this->resetPage();

            session()->flash('status', 'Teacher successfully created');

            $this->emit('hide-upsert-teacher-modal');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Creating teacher operation failed";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();
            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);

            $this->emit('hide-upsert-teacher-modal');

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

        $this->selectedSubjects = array_fill_keys($teacher->subjects->pluck('id')->all(), 'true');

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

            $this->authorize('update', $teacher);

            $this->authorize('update', $teacher->auth);

            DB::transaction(function()use($teacher, $data){

                // Update teacher
                $teacher->update($data);

                // Update user
                $teacher->auth->update($data);

                if(isset($data['selectedSubjects']) && !is_null($data['selectedSubjects'])){

                    $payload = array_filter($data['selectedSubjects'], function($value, $key){
                        return $value == 'true';
                    }, ARRAY_FILTER_USE_BOTH);

                    $teacher->subjects()->sync(array_keys($payload));

                }

            });

            $this->reset(['teacherId', 'userId', 'name', 'email', 'phone', 'employer', 'tsc_number', 'selectedSubjects']);

            $this->resetPage();

            session()->flash('status', "The teacher, {$teacher->auth->name} Successfully Updated");

            $this->emit('hide-upsert-teacher-modal');   

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Updating teacher operation failed";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();
            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);

            $this->emit('hide-upsert-teacher-modal');
            
        }
        
    }

    /**
     * Show the modal for deleting a teacher confirmation
     * 
     * @param Teacher $teacher
     */
    public function showDeleteTeacherModal(Teacher $teacher)
    {

        $this->teacherId = $teacher->id;

        $this->name = $teacher->auth->name;

        $this->emit('show-delete-teacher-modal');
        
    }

    /**
     * Trashing a teacher
     */
    public function deleteTeacher()
    {
        try {

            /** @var Teacher */
            $teacher = Teacher::findOrFail($this->teacherId);

            $this->authorize('delete', $teacher);
            
            DB::transaction(function()use($teacher){

                // if($teacher->auth) $teacher->auth->delete();

                $teacher->delete();
            });

            $this->reset(['teacherId', 'name']);

            $this->resetPage();

            session()->flash('status', "Teacher, {$teacher->auth->name}, has been succefully deleted");

            $this->emit('hide-delete-teacher-modal');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Deleting teacher operation failed";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();
            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);

            $this->emit('hide-delete-teacher-modal');
            
        }
        
    }

    /**
     * Restore a trashed teacher
     * 
     * @return mixed $teacherId
     */
    public function restoreTeacher($teacherId)
    {

        try {
            
            /** @var Teacher */
            $teacher = Teacher::where('id', $teacherId)->withTrashed()->firstOrFail();

            $this->authorize('restore', $teacher);

            $teacher->restore();

            session()->flash('status', "Teacher {$teacher->auth->name}, has been restored");


        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Restoring teacher operation failed";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();
            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);
            
        }
        
    }

    /**
     * Destroy a trashed teacher and remove them from the database
     * 
     * @param mixed $teacherId
     */
    public function destroyTeacher($teacherId)
    {

        try {
            
            /** @var Teacher */
            $teacher = Teacher::where('id', $teacherId)->withTrashed()->firstOrFail();

            $this->authorize('forceDelete', $teacher);

            $this->authorize('forceDelete', $teacher->auth);

            DB::transaction(function() use($teacher){

                $teacher->auth->forceDelete();

                $teacher->forceDelete();

            });

            session()->flash('status', "The teacher has been deleted from the system");
            
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Restoring teacher operation failed";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();
            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);
            
        }
        
    }
}
