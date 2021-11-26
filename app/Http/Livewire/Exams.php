<?php

namespace App\Http\Livewire;

use App\Models\Exam;
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

    public function render()
    {
        return view('livewire.exams', [
            'exams' => $this->getPaginatedExams(),
            'terms'=>$this->getTerms()
        ]);
    }

    public function getTerms()
    {
        return Exam::termOptions();
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
            'term' => ['bail', 'nullable'],
            'shortname' => ['bail', 'nullable'],
            'year' => ['bail', 'nullable'],
            'start_date' => ['bail', 'nullable'],
            'end_date' => ['bail', 'nullable'],
            'weight' => ['bail', 'nullable'],
            'counts' => ['bail', 'nullable']
        ];
    }

    function createExam()
    {
       $data = $this->validate();
        
        try {

            Exam::create([
                'name'=>$this->name,
                'term'=>$this->term,
                'shortname'=>$this->shortname,
                'year'=>$this->year,
                'start_date'=>$this->start_date,
                'end_date'=>$this->end_date,
                'weight'=>$this->weight,
                'counts'=>boolval($this->counts)
            ]);
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

        }
        $this->emit('hide-upsert-exam-modal');
    }


    public function updateExam()
    {
        $data = $this->validate();

        try {

            /** @var User */
            $exam = Exam::findOrFail($this->examId);

            if($exam->update($data)){

                session()->flash('status', 'exam successfully updated');

                $this->emit('hide-upsert-exam-modal');
            }
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'user-id' => $this->examId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

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

            $exam = Exam::findOrFail($this->examId);

            if($exam->delete()){

                $this->reset(['examId', 'name']);

                session()->flash('status', 'The exam has been successfully deleted');

                $this->emit('hide-delete-exam-modal');
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
}
