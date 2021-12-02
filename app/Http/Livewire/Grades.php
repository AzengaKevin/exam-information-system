<?php

namespace App\Http\Livewire;

use App\Models\Grade;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class Grades extends Component
{
    public $gradeId;

    public $low;
    public $high;
    public $grade;
    public $points;

    public function render()
    {
        return view('livewire.grades', [
            'grades' => $this->getPaginatedGrades()
        ]);
    }

    public function getPaginatedGrades()
    {
        return Grade::where('exam_id',NULL)->get();
    }

    /**
     * Show upsert user modal for editing and updating user
     * 
     * @param User $user
     */
    public function editGrade(Grade $grade)
    {
        
        $this->gradeId = $grade->id;

        $this->low = $grade->low;
        $this->high = $grade->high;
        $this->grade = $grade->grade;
        $this->points = $grade->points;

        $this->emit('show-upsert-grade-modal');
    }

    public function rules()
    {
        return [
            'low' => ['bail', 'required'],
            'high' => ['bail', 'required'],
            'points' => ['bail', 'required'],
            'grade' => ['bail', 'required'],
        ];
    }

    function createGrade()
    {
        $this->validate();
        try {
            Grade::create([
                'low'=>$this->low,
                'high'=>$this->high,
                'grade'=>$this->grade,
                'points'=>$this->points,
            ]);
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'user-id' => $this->gradeId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

        }
        $this->emit('hide-upsert-grade-modal');
    }


    public function updateGrade()
    {
        $data = $this->validate();

        try {

            /** @var User */
            $grade = Grade::findOrFail($this->gradeId);

            if($grade->update($data)){

                session()->flash('status', 'grade successfully updated');

                $this->emit('hide-upsert-grade-modal');
            }
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'user-id' => $this->gradeId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

        }
    }

    public function showDeleteGradeModal(Grade $grade)
    {
        $this->grade = $grade->id;

        $this->points = $grade->points;

        $this->emit('show-delete-grade-modal');
        
    }

    public function deleteGrade(Grade $grade)
    {
        try {

            $grade = Grade::findOrFail($this->departmtneId);

            if($grade->delete()){

                $this->reset(['gradeId', 'points']);

                session()->flash('status', 'The grade has been successfully deleted');

                $this->emit('hide-delete-grade-modal');
            }

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'grade-id' => $this->departmtneId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

            session()->flash('error', 'A fatal error has occurred');

            $this->emit('hide-delete-grade-modal');
        }
    }
}
