<?php

namespace App\Http\Livewire;

use App\Models\Department;
use App\Models\Subject;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

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
        return Subject::paginate(24);
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

    public function rules()
    {
        return [
            'name' => ['bail', 'required','string'],
            'description' => ['bail', 'nullable'],
            'shortname' => ['bail', 'nullable'],
            'subject_code' => ['bail', 'nullable'],
            'department_id' => ['bail', 'nullable'],

        ];
    }

    function createSubject()
    {
        $this->validate();
        
        try {

            Subject::create([
                'name'=>$this->name,
                'shortname'=>$this->shortname,
                'subject_code'=>$this->subject_code,
                'description'=>$this->description ,
                'slug'=>Str::slug($this->name),
                'department_id'=>$this->department_id
            ]);

            $this->reset(['name','shortname','subject_code','description','department_id']);
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [

                'action' => __CLASS__ . '@' . __METHOD__
            ]);

        }
        $this->emit('hide-upsert-subject-modal');
    }


    public function updateSubject()
    {
        $data = $this->validate();

        try {

            /** @var User */
            $subject = Subject::findOrFail($this->subjectId);

            if($subject->update($data)){

                session()->flash('status', 'subject successfully updated');

                $this->emit('hide-upsert-subject-modal');
            }
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'user-id' => $this->subjectId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

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
}
