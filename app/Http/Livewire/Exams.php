<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use App\Models\Level;
use App\Models\Subject;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Collection;

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

    /** @var GeneralSettings */
    protected $generalSettings;

    /**
     * Creates the exams component
     * 
     * @return Exams
     */
    public function __construct() {
        $this->generalSettings = app(GeneralSettings::class);
    }

    /**
     * Lifecycle method called when the component is mounting
     */
    public function mount()
    {
        $this->year = $this->generalSettings->current_academic_year;
        $this->term = $this->generalSettings->current_term;
    }

    /**
     * Lifecycle method to render and re-render the component when the component state changes
     * 
     * @return View
     */
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

    /**
     * Get the available term options
     * 
     * @return array
     */
    public function getTerms()
    {
        return Exam::termOptions();
    }
    
    /**
     * Get all school levels from the database
     * 
     * @return Collection
     */
    public function getLevels()
    {
        return Level::all(['id', 'name']);
    }

    /**
     * Get all subjects from the database
     * 
     * @return Collection
     */
    public function getSubjects()
    {
        return Subject::all(['id', 'name']);
    }

    /**
     * Get paginated exams from the database
     * 
     * @return Paginator
     */
    public function getPaginatedExams()
    {
        return Exam::latest()->paginate(24);
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
        $this->status = $exam->status;

        // Mark selected subjects
        foreach ($exam->subjects as $subject) {
            $this->selectedSubjects[$subject->id] = 'true';
        }

        // Mark selected levels
        foreach ($exam->levels as $level) {
            $this->selectedLevels[$level->id] = 'true';
        }

        $this->emit('show-upsert-exam-modal');
    }

    /**
     * Exams general fields validation rules
     * 
     * @return array
     */
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
            'selectedLevels' => ['nullable', 'array'],
            'selectedSubjects' => ['nullable', 'array']
        ];
    }

    /**
     * Creates a new exam entry to the database and enroll subjects and levels if applicable
     */
    public function createExam()
    {
       $data = $this->validate();

        try {

            $access = Gate::inspect('create', Exam::class);

            if($access->allowed()){

                unset($data['status']);

                DB::beginTransaction();
    
                $exam = Exam::create($data);
    
                if($exam){

                    if (isset($data['selectedSubjects']) && !empty($data['selectedSubjects'])) {

                        $selectedSubjectsData = array_filter($data['selectedSubjects'], function($value, $key){
                            return $value == 'true';
                        }, ARRAY_FILTER_USE_BOTH);
    
                        $exam->subjects()->sync(array_keys($selectedSubjectsData));
                    }

                    if (isset($data['selectedLevels']) && !empty($data['selectedLevels'])) {

                        $selectedLevelData = array_filter($data['selectedLevels'], function($value, $key){
                            return $value == 'true';
                        }, ARRAY_FILTER_USE_BOTH);

                        $exam->levels()->sync(array_keys($selectedLevelData));
                        
                    }

                    DB::commit();

                    $this->reset();
    
                    $this->resetPage();
    
                    session()->flash('status', 'Exam has been successfully created');
    
                    $this->emit('hide-upsert-exam-modal');
    
                }

            }else{

                session()->flash('error', $access->message());
    
                $this->emit('hide-upsert-exam-modal');

            }
            
        } catch (\Exception $exception) {

            DB::rollBack();
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            session()->flash('error', $exception->getMessage());

            $this->emit('hide-upsert-exam-modal');

        }
    }

    /**
     * Updates an exam database record with enrolled subjects and levels if applicable
     */
    public function updateExam()
    {
        $data = $this->validate();

        try {

            /** @var Exam */
            $exam = Exam::findOrFail($this->examId);

            $access = Gate::inspect('update', $exam);

            if($access->allowed()){

                DB::beginTransaction();

                if($exam->update($data)){

                    if (isset($data['selectedSubjects']) && !empty($data['selectedSubjects'])) {

                        $selectedSubjectsData = array_filter($data['selectedSubjects'], function($value, $key){
                            return $value == 'true';
                        }, ARRAY_FILTER_USE_BOTH);
    
                        $exam->subjects()->sync(array_keys($selectedSubjectsData));
                    }

                    if (isset($data['selectedLevels']) && !empty($data['selectedLevels'])) {

                        $selectedLevelData = array_filter($data['selectedLevels'], function($value, $key){
                            return $value == 'true';
                        }, ARRAY_FILTER_USE_BOTH);

                        $exam->levels()->sync(array_keys($selectedLevelData));
                        
                    }

                    DB::commit();
    
                    $this->reset();
    
                    session()->flash('status', 'Exam successfully updated');
    
                    $this->emit('hide-upsert-exam-modal');
                }

            }else{

                session()->flash('message', $access->message());
    
                $this->emit('hide-upsert-exam-modal');

            }

            
        } catch (\Exception $exception) {

            DB::rollBack();
            
            Log::error($exception->getMessage(), [
                'exam-id' => $this->examId,
                'action' => __METHOD__
            ]);

            session()->flash('error', $exception->getMessage());

            $this->emit('hide-upsert-exam-modal');

        }
    }

    /**
     * Show the modal for deleting an exam
     * 
     * @param Exam $exam
     */
    public function showDeleteExamModal(Exam $exam)
    {
        $this->examId = $exam->id;

        $this->name = $exam->name;

        $this->emit('show-delete-exam-modal');
    }

    /**
     * Soft delete an exam, Trash an exam
     */
    public function deleteExam()
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
                'action' => __METHOD__
            ]);

            session()->flash('error', $exception->getMessage());

            $this->emit('hide-delete-exam-modal');
        }
    }
    
    /**
     * Show modal for enrolling levels to an exam
     * 
     * @param Exam $exam
     */
    public function showEnrollLevelsModal(Exam $exam)
    {
        $this->examId = $exam->id;

        $this->shortname = $exam->shortname;

        foreach ($exam->levels as $level) {
            $this->selectedLevels[$level->id] = 'true';
        }

        $this->emit('show-enroll-levels-modal');
    }

    /**
     * Update the enrolled levels for an exam
     */
    public function updateExamLevels()
    {

        $data = $this->validate(['selectedLevels' => ['array', 'min:1']]);

        try {

            /** @var Exam */
            $exam = Exam::findOrFail($this->examId);

            $access = Gate::inspect('update', $exam);

            if ($access->allowed()) {

                $selectedLevelData = array_filter($data['selectedLevels'], function($value, $key){
                    return $value == 'true';
                }, ARRAY_FILTER_USE_BOTH);
                
                $exam->levels()->sync(array_keys($selectedLevelData));

                session()->flash('status', 'Exam enrolled levels hav been successfully updated');

                $this->emit('hide-enroll-levels-modal');

            }else{

                session()->flash('message', $access->message());

                $this->emit('hide-enroll-levels-modal');
            }
    

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__,
                'exam-id' => $this->examId
            ]);

            session()->flash('error', $exception->getMessage());

            $this->emit('hide-enroll-levels-modal');
        }
        
    }

    /**
     * Show modal to enroll subjects to an exam
     * 
     * @param Exam $exam
     */
    public function showEnrollSubjectsModal(Exam $exam)
    {
        $this->examId = $exam->id;

        $this->shortname = $exam->shortname;

        foreach ($exam->subjects as $subject) {
            $this->selectedSubjects[$subject->id] = 'true';
        }

        $this->emit('show-enroll-subjects-modal');
    }

    /**
     * Updated subjects enrolled to an exam
     */
    public function enrollSubjects()
    {
        $data = $this->validate(['selectedSubjects' => ['bail', 'array', 'min:1']]);

        try {

            /** @var Exam */
            $exam = Exam::findOrFail($this->examId);

            $access = Gate::inspect('update', $exam);

            if($access->allowed()){

                $selectedSubjectsData = array_filter($data['selectedSubjects'], function($value, $key){
                    return $value == 'true';
                }, ARRAY_FILTER_USE_BOTH);

                $exam->subjects()->sync(array_keys($selectedSubjectsData));
    
                session()->flash('status', 'Enrolling subjects to an exams successfully completed');
    
                $this->emit('hide-enroll-subjects-modal');
                
            }else{

                session()->flash('error', $access->message());
                
                $this->emit('hide-enroll-subjects-modal');
            }


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
