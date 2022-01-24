<?php

namespace App\Http\Livewire;

use App\Models\Grade;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class Grades extends Component
{
    use AuthorizesRequests;

    public $english_comment;
    public $swahili_comment;
    public $ct_comment;
    public $p_comment;
    public $grade;
    public $points;

    public $gradeId;

    public $trashed;

    /**
     * Component lifecycle method that executes only once when the component is mounting
     * 
     * @param string $trashed 
     */
    public function mount(string $trashed = null)
    {
        $this->trashed = boolval($trashed);
    }

    /**
     * Component lifecyle method that executes everytime the state of the component changes
     * 
     * @return View
     */
    public function render()
    {
        return view('livewire.grades', [
            'grades' => $this->getAllGrades()
        ]);
    }

    /**
     * Get all application grades
     * 
     * @return Collection
     */
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

    /**
     * Define model fields validation rules
     * 
     * @return array
     */
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

            $this->authorize('update', $grade);

            if($grade->update($data)){

                $this->reset();

                session()->flash('status', 'Grade been successfully updated');

                $this->emit('hide-update-grade-modal');
            }
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Sorry! Updating grade operation failed";
            
            if($exception instanceof AuthorizationException) $message = $exception->getMessage();
            
            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);

            $this->emit('hide-update-grade-modal');

        }
    }
}
