<?php

namespace App\Http\Livewire;

use App\Models\Subject;
use Livewire\Component;
use App\Models\Department;
use App\Models\Level;
use App\Rules\LowerAlphaOnly;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\App;

class Subjects extends Component
{
    use WithPagination, AuthorizesRequests;

    protected $paginationTheme = 'bootstrap';
    public $trashed = false;
    public $departments;

    public $departmentId;
    public $subjectId;

    public $name;
    public $shortname;
    public $subject_code;
    public $department_id;
    public $description;
    public $segments = array();

    public $teachers;
    public $levels;

    /**
     * Lifecycle method that executes once when the component is launching
     * 
     * @param string $trashed
     */
    public function mount(string $trashed = null)
    {
        $this->trashed = boolval($trashed);

        $this->teachers = collect([]);

        $this->departments = $this->getDepartments();

        $this->levels = $this->getAllLevels();
    }

    /**
     * Lifecyle method that renders the component everytime it's internal state changes
     * 
     * @return View
     */
    public function render()
    {
        return view('livewire.subjects', [
            'subjects' => $this->getPaginatedSubjects()
        ]);
    }

    /**
     * Get all database departments
     * 
     * @return Collection
     */
    public function getDepartments()
    {
        return Department::all(['id', 'name']);
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
     * Get paginated subjects from the database
     * 
     * @return Paginator
     */
    public function getPaginatedSubjects()
    {
        $subjectsQuery = Subject::with('teachers');

        if($this->trashed) $subjectsQuery->onlyTrashed();

        return $subjectsQuery->paginate(24);
    }

    /**
     * Show upsert user modal for editing and updating user
     * 
     * @param User $user
     */
    public function editSubject(Subject $subject)
    {
        
        $this->subjectId = $subject->id;

        $this->name = $subject->name;
        $this->shortname = $subject->shortname;
        $this->subject_code = $subject->subject_code;
        $this->department_id = $subject->department_id;
        $this->description = $subject->description;

        if(!empty($subject->segments)){
            foreach ($subject->segments as $level_id => $segments) {
                foreach ($segments as $key => $value) {                    
                    array_push($this->segments, [
                        'level_id' => $level_id,
                        'key' => $key,
                        'value' => $value
                    ]);
                }
            }
        }

        $this->emit('show-upsert-subject-modal');
    }

    /**
     * Show a modal of teachers that teach that subject
     * 
     * @param Subject $subject
     */
    public function showTeachers(Subject $subject)
    {
        $this->name = $subject->name;

        $this->teachers = $subject->teachers;

        $this->emit('show-subject-teachers-modal');
        
    }

    /**
     * Subjects fields validation rules
     * 
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['bail', 'required','string', Rule::unique('subjects')->ignore($this->subjectId)],
            'description' => ['bail', 'nullable'],
            'shortname' => ['bail', 'required', 'max:5', new LowerAlphaOnly],
            'subject_code' => ['bail', 'nullable'],
            'department_id' => ['bail', 'nullable'],
            'segments' => ['bail', 'nullable', 'array'],
            'segments.*.level_id' => ['bail', 'required'],
            'segments.*.key' => ['bail', 'required'],
            'segments.*.value' => ['bail', 'required', 'integer']
        ];
    }

    /**
     * Creates a subject entry in t he database based on the user input
     */
    public function createSubject()
    {
        
        $data = $this->validate();

        try {
            
            $this->authorize('create', Subject::class);

            if(isset($data['segments']) && !empty($data['segments'])){

                $segments = array();

                foreach ($data['segments'] as $item){

                    if(!array_key_exists($item['level_id'], $segments))
                        $segments[$item['level_id']] = array();

                    $segments[$item['level_id']][$item['key']] = $item['value'];
                } 

                $data['segments'] = $segments;
            }

            /** @var Subject */
            $subject = Subject::create($data);

            if($subject){

                $this->reset(['name','shortname','subject_code','description','department_id', 'segments']);

                $this->resetValidation();

                $this->resetPage();
    
                session()->flash('status', 'Subject successfully created');
    
                $this->emit('hide-upsert-subject-modal');

            }
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Creating subject operation failed";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();
            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);

            $this->emit('hide-upsert-subject-modal');

        }
    }

    /**
     * Update a database subject record based on user input
     */
    public function updateSubject()
    {
        $data = $this->validate();

        if(isset($data['segments']) && !empty($data['segments'])){

            $segments = array();

            foreach ($data['segments'] as $item){

                if(!array_key_exists($item['level_id'], $segments))
                    $segments[$item['level_id']] = array();

                $segments[$item['level_id']][$item['key']] = $item['value'];
            } 

            $data['segments'] = $segments;
        }

        try {

            /** @var User */
            $subject = Subject::findOrFail($this->subjectId);

            $this->authorize('update', $subject);

            if($subject->update($data)){

                $this->reset(['subjectId', 'name','shortname','subject_code','description','department_id', 'segments']);

                session()->flash('status', 'Subject successfully updated');

                $this->emit('hide-upsert-subject-modal');
            }
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Creating subject operation failed";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();
            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);

            $this->emit('hide-upsert-subject-modal');

        }
    }

    /**
     * Show the confirmation modal for deleting a subject
     * 
     * @param Subject $subject
     */
    public function showDeleteSubjectModal(Subject $subject)
    {
        $this->departmtneId = $subject->id;

        $this->name = $subject->name;

        $this->emit('show-delete-subject-modal');
        
    }

    /** 
     * Add another segment field to the segments fieldset
     */
    public function addSegmentFields()
    {
        array_push($this->segments, array(
            'level_id' => null,
            'key' => null,
            'value' => null
        ));
        
    }

    /** 
     * Removes the fields at the specified position
     * 
     * @param int $index
     */
    public function removeSegmentFields(int $index)
    {
        array_splice($this->segments, $index, 1);

        $this->segments = array_values($this->segments);
        
    }

    /**
     * Trash a subject
     */
    public function deleteSubject()
    {
        try {

            /** @var Subject */
            $subject = Subject::findOrFail($this->departmtneId);

            $this->authorize('delete', $subject);

            if($subject->delete()){

                $this->reset(['subjectId', 'name']);

                session()->flash('status', 'The subject has been successfully deleted');

                $this->emit('hide-delete-subject-modal');
            }

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Deleting subject operation failed";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();

            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);

            $this->emit('hide-delete-subject-modal');
        }
    }

    /**
     * Truncates the subjects table | Deletes all the records from the subjects tabl
     */
    public function truncateSubjects()
    {
        try {
            
            $this->authorize('bulkDelete', Subject::class);
            
            /** @var Collection */
            $subjects = Subject::all();

            $subjects->each(function(Subject $subject){
                $subject->delete();
            });

            session()->flash('status', 'You\'ve successfully deleted all the subjects in the application');

            $this->emit('hide-truncate-subjects-modal');

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Bulk deleting subjects operation failed";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();

            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);

            $this->emit('hide-truncate-subjects-modal');
            
        }
    }

    /**
     * Restoring a subject that has been trashed
     * 
     * @param mixed $subjectId
     */
    public function restoreSubject($subjectId)
    {
        try {

            /** @var Subject */
            $subject = Subject::where('id', $subjectId)->withTrashed()->firstOrFail();

            $this->authorize('restore', $subject);

            $subject->restore();

            session()->flash('status', "The subject, {$subject->name}, has been restored");
            

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Bulk deleting subjects operation failed";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();

            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);
            
        }
        
    }
    
    /**
     * Deleting a subject from the system
     * 
     * @param mixed $subjectId
     */
    public function destroySubject($subjectId)
    {
        try {

            /** @var Subject */
            $subject = Subject::where('id', $subjectId)->withTrashed()->firstOrFail();

            $this->authorize('forceDelete', $subject);

            $subject->forceDelete();

            session()->flash('status', "The subject, {$subject->name}, has been deleted from the system");
            

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Bulk deleting subjects operation failed";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();

            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);
            
        }
        
    }
}
