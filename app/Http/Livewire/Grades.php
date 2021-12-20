<?php

namespace App\Http\Livewire;

use App\Models\Grade;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class Grades extends Component
{

    public $english_comment;
    public $swahili_comment;
    public $grade;
    public $points;

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


    public function updateGrade()
    {
        $data = $this->validate();

        try {

            /** @var Grade */
            $grade = Grade::findOrFail($this->gradeId);

            if($grade->update($data)){

                session()->flash('status', 'grade been successfully updated');

                $this->emit('hide-upsert-grade-modal');
            }
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'user-id' => $this->gradeId,
                'action' => __METHOD__
            ]);

        }
    }
}
