<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use App\Models\Level;
use App\Models\Subject;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class Exams extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $examId;

    public $name;
    public $term;
    public $shortname;
    public $year;
    public $start_date;
    public $end_date;
    public $weight;
    public $counts;
    public $description;
    public $status;

    public $selectedLevels = [];
    public $selectedSubjects = [];

    public function render()
    {
        return view('livewire.exams', [
            'exams' => $this->getPaginatedExams(),
            'terms'=> $this->getTerms(),
            'levels' => $this->getLevels(),
            'subjects' => $this->getSubjects(),
            'examStatusOptions'=>Exam::examStatusOptions()
        ]);
    }

    public function getTerms()
    {
        return Exam::termOptions();
    }
    
    public function getLevels()
    {
        return Level::all(['id', 'name']);
    }

    public function getSubjects()
    {
        return Subject::all(['id', 'name']);
    }

    public function getPaginatedExams()
    {
        return Exam::latest()->paginate(16);
    }

    /**
     * Show upsert user modal for editing and updating user
     * 
     * @param User $user
     */
    public function editExam(Exam $exam)
    {
        
        $this->examId = $exam->id;
        
        $this->name = $exam->name;
        $this->term = $exam->term;
        $this->shortname = $exam->shortname;
        $this->year = $exam->year;
        $this->start_date = $exam->start_date;
        $this->end_date = $exam->end_date;
        $this->weight = $exam->weight;
        $this->counts = $exam->counts;


        $this->emit('show-upsert-exam-modal');
    }

    public function rules()
    {
        return [
            'name' => ['bail', 'required', 'string', Rule::unique('exams')->ignore($this->examId)],
            'term' => ['bail', 'nullable', Rule::in(Exam::termOptions())],
            'shortname' => ['bail', 'required', 'string', 'max:20',Rule::unique('exams')->ignore($this->examId)],
            'year' => ['bail', 'required'],
            'start_date' => ['bail', 'nullable'],
            'end_date' => ['bail', 'nullable'],
            'weight' => ['bail', 'nullable'],
            'counts' => ['bail', 'nullable'],
            'description' => ['nullable'],
            'status' => ['nullable'],
        ];
    }

    public function createExam()
    {
       $data = $this->validate();

        try {

            $access = Gate::inspect('create', Exam::class);

            if($access->allowed()){

                unset($data['status']);
    
                $exam = Exam::create($data);
    
                if($exam){
    
                    $this->reset();
    
                    $this->resetPage();
    
                    session()->flash('status', 'Exam has been successfully created');
    
                    $this->emit('hide-upsert-exam-modal');
    
                }

            }else{

                session()->flash('message', $access->message());
    
                $this->emit('hide-upsert-exam-modal');

            }
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

            session()->flash('error', 'A fatal error occurred while trying to add an exam');

            $this->emit('hide-upsert-exam-modal');

        }
    }


    public function updateExam()
    {
        $data = $this->validate();

        try {

            /** @var Exam */
            $exam = Exam::findOrFail($this->examId);

            $access = Gate::inspect('update', $exam);

            if($access->allowed()){

                if($exam->update($data)){
    
                    $this->reset();
    
                    session()->flash('status', 'Exam successfully updated');
    
                    $this->emit('hide-upsert-exam-modal');
                }

            }else{

                session()->flash('message', $access->message());
    
                $this->emit('hide-upsert-exam-modal');

            }

            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'exam-id' => $this->examId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

            session()->flash('error', 'A fatal error occurred while trying to add an exam');

            $this->emit('hide-upsert-exam-modal');

        }
    }

    public function showDeleteExamModal(Exam $exam)
    {
        $this->examId = $exam->id;

        $this->name = $exam->name;

        $this->emit('show-delete-exam-modal');
    }

    public function deleteExam(Exam $exam)
    {
        try {

            /** @var Exam */
            $exam = Exam::findOrFail($this->examId);

            $access = Gate::inspect('delete', $exam);

            if($access->allowed()){
    
                if($exam->delete()){
    
                    $this->reset(['examId', 'name']);
    
                    session()->flash('status', 'The exam has been successfully deleted');
    
                    $this->emit('hide-delete-exam-modal');
                }

            }else{

                session()->flash('message', $access->message());
    
                $this->emit('hide-upsert-exam-modal');

            }

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'exam-id' => $this->departmtneId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

            session()->flash('error', 'A fatal error has occurred');

            $this->emit('hide-delete-exam-modal');
        }
    }
    
    public function showEnrollLevelsModal(Exam $exam)
    {
        $this->examId = $exam->id;

        $this->shortname = $exam->shortname;

        foreach ($exam->levels as $level) {

            $this->selectedLevels[$level->id] = 'true';
            
        }

        $this->emit('show-enroll-levels-modal');
    }

    public function updateExamLevels()
    {

        $data = $this->validate([
            'selectedLevels' => ['array', 'min:1']
        ]);

        $selectedLevelData = array_filter($data['selectedLevels'], function($value, $key){
            return $value == 'true';
        }, ARRAY_FILTER_USE_BOTH);

        try {

            /** @var Exam */
            $exam = Exam::findOrFail($this->examId);

            $exam->levels()->sync(array_keys($selectedLevelData));

            $this->emit('hide-enroll-levels-modal');


        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__,
                'exam-id' => $this->examId
            ]);

            session()->flash('error', 'An error occurred when enrolling levels to an exam');

            $this->emit('hide-enroll-levels-modal');
        }
        
    }

    public function showEnrollSubjectsModal(Exam $exam)
    {
        $this->examId = $exam->id;

        $this->shortname = $exam->shortname;

        foreach ($exam->subjects as $subject) {

            $this->selectedSubjects[$subject->id] = 'true';
            
        }


        $this->emit('show-enroll-subjects-modal');
    }

    public function enrollSubjects()
    {
        $data = $this->validate([
            'selectedSubjects' => ['bail', 'array', 'min:1']
        ]);

        $selectedSubjectsData = array_filter($data['selectedSubjects'], function($value, $key){
            return $value == 'true';
        }, ARRAY_FILTER_USE_BOTH);

        try {

            /** @var Exam */
            $exam = Exam::findOrFail($this->examId);

            $exam->subjects()->sync(array_keys($selectedSubjectsData));

            session()->flash('status', 'Enrolling subjects to an exams successfully completed');

            $this->emit('hide-enroll-subjects-modal');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__,
                'exam-id' => $exam->id
            ]);

            session()->flash('error', 'Enrolling subjects to an exam failed, contact admin');

            $this->emit('hide-enroll-subjects-modal');
        }
        
    }

}
