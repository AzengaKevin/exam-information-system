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
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class Exams extends Component
{
    use WithPagination, AuthorizesRequests;

    protected $paginationTheme = 'bootstrap';

    public $levels;
    public $subjects;

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
    public $deviation_exam_id;

    public $selectedLevels = [];
    public $selectedSubjects = [];

    /** @var GeneralSettings */
    protected $generalSettings;

    public $trashed = false;

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
     * 
     * @param string $trashed
     */
    public function mount(string $trashed = null)
    {
        $this->trashed = boolval($trashed);

        $this->year = $this->generalSettings->current_academic_year;
        $this->term = $this->generalSettings->current_term;

        $this->levels = $this->getLevels();
        $this->subjects = $this->getSubjects();
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
            'otherExams' => $this->getOtherExams(),
            'terms'=> $this->getTerms(),
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
        $examsQuery = Exam::with(['levels', 'subjects'])->latest();

        if($this->trashed) $examsQuery->onlyTrashed();

        return $examsQuery->paginate(24)->withQueryString();
    }

    /**
     * Get the other exam from the database apart from the current one
     * 
     * @return Collection
     */
    public function getOtherExams()
    {
        return Exam::status('Published')->where('id', '!=', $this->examId)
            ->latest()->limit(10)
            ->get();
    }

    /**
     * Show upsert exam modal for updating an exam
     * 
     * @param Exam $exam
     */
    public function editExam(Exam $exam)
    {
        
        $this->examId = $exam->id;
        
        $this->name = $exam->name;
        $this->term = $exam->term;
        $this->shortname = $exam->shortname;
        $this->year = $exam->year;
        $this->start_date = optional($exam->start_date)->format('Y-m-d');
        $this->end_date = optional($exam->end_date)->format('Y-m-d');
        $this->weight = $exam->weight;
        $this->counts = $exam->counts;
        $this->status = $exam->status;
        $this->deviation_exam_id = $exam->deviation_exam_id;
        $this->selectedSubjects = array_fill_keys($exam->subjects->pluck('id')->all(), 'true');
        $this->selectedLevels = array_fill_keys($exam->levels->pluck('id')->all(), 'true');

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
            'selectedSubjects' => ['nullable', 'array'],
            'deviation_exam_id' => ['nullable', 'integer']
        ];
    }

    /**
     * Creates a new exam entry to the database and enroll subjects and levels if applicable
     */
    public function createExam()
    {
        $data = $this->validate();

        try {

            $this->authorize('create', Exam::class);

            unset($data['status']);

            /** @var Exam */
            $deviationExam = Exam::find($data['deviation_exam_id']);

            unset($data['deviation_exam_id']);

            DB::transaction(function() use($data, $deviationExam){

                /** @var Exam */
                $exam = Exam::create($data);

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

                if(!is_null($deviationExam) && $exam->matches($deviationExam)) $exam->update(['deviation_exam_id' => $deviationExam->id]);

                $exam->userActivities()->attach(Auth::id(), ['action' => 'Created The Exam']);
                
            });

            $this->reset();
    
            $this->resetPage();

            session()->flash('status', "The exam has been successfully created");

            $this->emit('hide-upsert-exam-modal');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry, creating exam action failed, contact admin if this persists");

            $this->emit('hide-upsert-exam-modal');

        }
    }

    /**
     * Set appropriate error based on the type of the error and the environment
     * 
     * @param \Exception $exception
     * @param string $message
     */
    private function setError(\Exception $exception, string $message)
    {
        if($exception instanceof AuthorizationException) $message = $exception->getMessage();

        else $message = App::environment('local') ? $exception->getMessage() : $message;

        session()->flash('error', $message);
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

            $this->authorize('update', $exam);

            /** @var Exam */
            $deviationExam = Exam::find($data['deviation_exam_id']);

            unset($data['deviation_exam_id']);

            DB::transaction(function() use($exam, $data, $deviationExam){

                $exam->update($data);

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

                if(!is_null($deviationExam) && $exam->matches($deviationExam)){
                    $exam->update(['deviation_exam_id' => $deviationExam->id]);
                }

                $exam->userActivities()->attach(Auth::id(), ['action' => 'Updated The Exam']);
                    
            });
            
            $this->reset();

            session()->flash('status', "The exam, {$exam->name}, has been successfully updated");

            $this->emit('hide-upsert-exam-modal');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'exam-id' => $this->examId,
                'action' => __METHOD__
            ]);
            
            $this->setError($exception, "Sorry, updating the exam operation failed");

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

            $this->authorize('delete', $exam);

            if($exam->delete()){

                $this->reset(['examId', 'name']);

                session()->flash('status', 'The exam has been successfully deleted');

                $this->emit('hide-delete-exam-modal');
            }

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'exam-id' => $this->departmtneId,
                'action' => __METHOD__
            ]);
            
            $this->setError($exception, "Sorry, deleting the exam operation failed");;

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

            $this->authorize('update', $exam);

            $selectedLevelData = array_filter($data['selectedLevels'], function($value, $key){
                return $value == 'true';
            }, ARRAY_FILTER_USE_BOTH);

            DB::transaction(function() use($exam, $selectedLevelData){
                
                $exam->levels()->sync(array_keys($selectedLevelData));

                $exam->userActivities()->attach(Auth::id(), [
                    'action' => "Updated Exam Levels"
                ]);

            });

            session()->flash('status', 'Exam enrolled levels hav been successfully updated');

            $this->emit('hide-enroll-levels-modal');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__,
                'exam-id' => $this->examId
            ]);
            
            $this->setError($exception, "Sorry, updating the exam operation failed");

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

            $this->authorize('update', $exam);
            
            $selectedSubjectsData = array_filter($data['selectedSubjects'], function($value, $key){
                return $value == 'true';
            }, ARRAY_FILTER_USE_BOTH);

            DB::transaction(function() use($exam, $selectedSubjectsData){

                $exam->subjects()->sync(array_keys($selectedSubjectsData));

                $exam->userActivities()->attach(Auth::id(), [
                    'action' => "Updated Exam Subjects"
                ]);
                
            });

            session()->flash('status', 'Enrolling subjects to an exams successfully completed');

            $this->emit('hide-enroll-subjects-modal');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__,
                'exam-id' => $exam->id
            ]);
            
            $this->setError($exception, "Sorry, enrolling subjects to an exam failed, contact admin");

            $this->emit('hide-enroll-subjects-modal');
        }
        
    }

    /**
     * Restore a trashed exam
     * 
     * @param mixed $examId
     */
    public function restoreExam($examId)
    {
        try {

            /** @var Exam */
            $exam = Exam::where('id', $examId)->withTrashed()->firstOrFail();

            $this->authorize('restore', $exam);

            $exam->restore();

            session()->flash('status', "The exam, {$exam->name}, has been restored");

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__,
                'exam-id' => $exam->id
            ]);
            
            $this->setError($exception, "Sorry, restoring an exam failed, contact admin");

        }
    }

    /**
     * Completely delete an exam from the database
     * 
     * @param mixed $examId
     */
    public function destroyExam($examId)
    {
        try {

            /** @var Exam */
            $exam = Exam::where('id', $examId)->withTrashed()->firstOrFail();

            $this->authorize('forceDelete', $exam);

            $exam->forceDelete();

            session()->flash('status', "The exam, {$exam->name}, has been deleted completely");

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__,
                'exam-id' => $exam->id
            ]);
            
            $this->setError($exception, "Sorry, deleting an exam failed, contact admin");

        }        
    }
}
