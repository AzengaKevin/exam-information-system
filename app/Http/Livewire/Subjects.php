<?php

namespace App\Http\Livewire;

use App\Models\Subject;
use Livewire\Component;
use App\Models\Department;
use App\Rules\LowerAlphaOnly;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

class Subjects extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $departmentId;
    public $subjectId;

    public $name;
    public $shortname;
    public $subject_code;
    public $department_id;
    public $description;

    public $teachers;

    public function mount()
    {
        $this->teachers = collect([]);
    }

    public function render()
    {
        return view('livewire.subjects', [
            'departments'=>$this->getDepartments(),
            'subjects' => $this->getPaginatedSubjects()
        ]);
    }

    public function getDepartments()
    {
        return Department::get();
    }

    public function getPaginatedSubjects()
    {
        return Subject::with(['teachers'])->paginate(24);
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

        $this->emit('show-upsert-subject-modal');
    }

    public function showTeachers(Subject $subject)
    {
        $this->name = $subject->name;

        $this->teachers = $subject->teachers;

        $this->emit('show-subject-teachers-modal');
        
    }

    public function rules()
    {
        return [
            'name' => ['bail', 'required','string', Rule::unique('subjects')->ignore($this->subjectId)],
            'description' => ['bail', 'nullable'],
            'shortname' => ['bail', 'required', 'max:5', new LowerAlphaOnly],
            'subject_code' => ['bail', 'nullable'],
            'department_id' => ['bail', 'nullable'],
        ];
    }

    /**
     * Creates a subject entry in t he database based on the user input
     */
    public function createSubject()
    {
        $data = $this->validate();
        
        try {

            Subject::create($data);

            $this->reset(['name','shortname','subject_code','description','department_id']);

            session()->flash('status', 'Subject successfully created');

            $this->emit('hide-upsert-subject-modal');
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [

                'action' => __METHOD__
            ]);

            session()->flash('error', 'Subject creation failed');

            $this->emit('hide-upsert-subject-modal');

        }
    }

    /**
     * Update a database subject record based on user input
     */
    public function updateSubject()
    {
        $data = $this->validate();

        try {

            /** @var User */
            $subject = Subject::findOrFail($this->subjectId);

            if($subject->update($data)){

                $this->reset(['subjectId', 'name','shortname','subject_code','description','department_id']);

                session()->flash('status', 'subject successfully updated');

                $this->emit('hide-upsert-subject-modal');
            }
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'user-id' => $this->subjectId,
                'action' => __METHOD__
            ]);

            session()->flash('error', 'A fatal subject error occurred');

            $this->emit('hide-upsert-subject-modal');

        }
    }

    public function showDeleteSubjectModal(Subject $subject)
    {
        $this->departmtneId = $subject->id;

        $this->name = $subject->name;

        $this->emit('show-delete-subject-modal');
        
    }

    public function deleteSubject(Subject $subject)
    {
        try {

            $subject = Subject::findOrFail($this->departmtneId);

            if($subject->delete()){

                $this->reset(['subjectId', 'name']);

                session()->flash('status', 'The subject has been successfully deleted');

                $this->emit('hide-delete-subject-modal');
            }

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'subject-id' => $this->subjectId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

            session()->flash('error', 'A fatal error has occurred');

            $this->emit('hide-delete-subject-modal');
        }
    }

    /**
     * Truncates the subjects table | Deletes all the records from the subjects tabl
     */
    public function truncateSubjects()
    {
        try {
            
            //Subject::truncate();
            
            /** @var Collection */
            $subjects = Subject::all();

            $subjects->each(function(Subject $subject){
                $subject->forceDelete();
            });

            session()->flash('status', 'You\'ve successfully deleted all the subjects in the application');

            $this->emit('hide-truncate-subjects-modal');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', 'An error occurred while deleting subjects');

            $this->emit('hide-truncate-subjects-modal');
            
        }
        
    }
}
