<?php

namespace App\Http\Livewire;

use App\Models\Grade;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class Grades extends Component
{

    public $english_comment;
    public $swahili_comment;
    public $ct_comment;
    public $p_comment;
    public $grade;
    public $points;

    public $gradeId;

    public function render()
    {
        return view('livewire.grades', [
            'grades' => $this->getAllGrades()
        ]);
    }

    public function getAllGrades()
    {
        return Grade::all();
    }

    /**
     * Show upsert grade modal for editing and updating user
     * 
     * @param Grade $user
     */
    public function editGrade(Grade $grade)
    {
        $this->english_comment = $grade->english_comment;
        $this->swahili_comment = $grade->swahili_comment;
        $this->ct_comment = $grade->ct_comment;
        $this->p_comment = $grade->p_comment;
        $this->grade = $grade->grade;
        $this->points = $grade->points;

        $this->gradeId = $grade->id;

        $this->emit('show-update-grade-modal');
    }

    public function rules()
    {
        return [
            'english_comment' => ['bail', 'required'],
            'swahili_comment' => ['bail', 'required'],
            'ct_comment' => ['bail', 'required'],
            'p_comment' => ['bail', 'required'],
            'points' => ['bail', 'required', 'integer', 'between:0,12']
        ];
    }


    /** 
     * Update a database grade record
     */
    public function updateGrade()
    {
        $data = $this->validate();

        try {

            /** @var Grade */
            $grade = Grade::findOrFail($this->gradeId);

            if($grade->update($data)){

                $this->reset();

                session()->flash('status', 'grade been successfully updated');

                $this->emit('hide-update-grade-modal');
            }
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'grade-id' => $this->gradeId,
                'action' => __METHOD__
            ]);

            session()->flash('error', 'Updating Grade Failed');

            $this->emit('hide-update-grade-modal');

        }
    }
}
