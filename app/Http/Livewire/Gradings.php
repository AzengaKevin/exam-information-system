<?php

namespace App\Http\Livewire;

use App\Models\Grade;
use App\Models\Grading;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Gradings extends Component
{
    use AuthorizesRequests;

    public $gradingId;

    public $trashed = false;

    public $name;
    public $values = [];

    /**
     * Component lifecyle method that executes once, when the component is mounting
     * 
     * @param string $trashed
     */
    public function mount(string $trashed = null)
    {
        $this->trashed = boolval($trashed);
        /** @var Collection */
        $grades = Grade::all(['grade', 'points']);

        $grades->each(function($grade){
            array_push($this->values, [
                "grade" => $grade->grade,
                "points" => $grade->points,
                "min" => null,
                "max" => null,
            ]);
        });
        
    }

    /**
     * Lifecycle method that renders the component evrytime the stte of the componen changes
     * 
     * @return View
     */
    public function render()
    {
        return view('livewire.gradings', [
            'gradings' => $this->getAllGradings(),
            'grades' => Grading::gradeOptions()
        ]);
    }

    /**
     * Get all grading systems from the database
     */
    public function getAllGradings()
    {
        $gradingsQuery = Grading::query();

        if($this->trashed) $gradingsQuery->onlyTrashed();

        return $gradingsQuery->get();
    }

    /**
     * Compoent models properties validtion fields
     * 
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['bail', 'required', 'string', Rule::unique('gradings')->ignore($this->gradingId)],
            'values' => ['bail', 'array', 'min:12'],
            'values.*.min' => ['nullable', 'integer', 'between:0,100'],
            'values.*.max' => ['nullable', 'integer', 'between:0,100'],
            'values.*.grade' => ['nullable', 'string'],
            'values.*.points' => ['nullable', 'integer', 'between:0,12'],
        ];
    }

    /**
     * Adding a grading system enrty to the database
     */
    public function addGrading()
    {
        $data = $this->validate();

        try {

            $this->authorize('create', Grading::class);

            /** @var Grading */
            $grading = Grading::create($data);

            if($grading){

                $this->reset(['name', 'values']);

                $this->resetValidation();

                session()->flash('status', 'Grading system Has been successfully created');

                $this->emit('hide-upsert-grading-modal');
            }

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry, adding grading system operation failed");

            $this->emit('hide-upsert-grading-modal');
            
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
     * Show upsert grading model for updating the specified grading
     * 
     * @param Grading $grading
     */
    public function editGrading(Grading $grading)
    {
        $this->gradingId = $grading->id;

        $this->name = $grading->name;
        $this->values = $grading->values;

        $this->emit('show-upsert-grading-modal');
    }

    /**
     * Update a grading database entry
     */
    public function updateGrading()
    {
        $data = $this->validate();

        try {

            /** @var Grading */
            $grading = Grading::findOrFail($this->gradingId);

            $this->authorize('update', $grading);

            if ($grading->update($data)) {

                $this->reset(['gradingId', 'name', 'values']);

                $this->resetValidation();

                session()->flash('status', 'Grading system Has been successfully updated');

                $this->emit('hide-upsert-grading-modal');
                
            }
            
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry, updating grading system operation failed");

            $this->emit('hide-upsert-grading-modal');
            
        }
    }

    /**
     * Show confirmation modal for deleting a grading system
     * 
     * @param Grading $grading
     */
    public function showDeleteGradingModal(Grading $grading)
    {
        $this->gradingId = $grading->id;

        $this->name = $grading->name;

        $this->emit('show-delete-grading-modal');
    }

    /**
     * Trashing a grading system
     */
    public function deleteGrading()
    {

        try {

            /** @var Grading */
            $grading = Grading::findOrFail($this->gradingId);

            $this->authorize('delete', $grading);

            if ($grading->delete()) {

                $this->reset(['gradingId', 'name', 'values']);

                $this->resetValidation();

                session()->flash('status', 'Grading system Has been successfully deleted');

                $this->emit('hide-delete-grading-modal');
                
            }
            
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry, failed deleting a grading system");

            $this->emit('hide-delete-grading-modal');
            
        }
        
    }

    /**
     * Show the details of a grading system
     * 
     * @param Grading $grading
     */
    public function showGrading(Grading $grading)
    {
        $this->name = $grading->name;
        $this->values = $grading->values;

        $this->emit('show-grading-instance-modal');
        
    }

    /**
     * Restore a deleted grading system
     * 
     * @param mixed $gradingId
     */
    public function restoreGrading($gradingId)
    {
        try {
            /** @var Grading */
            $grading = Grading::where('id', $gradingId)->withTrashed()->firstOrFail();

            $this->authorize('restore', $grading);

            $grading->restore();

            session()->flash('status', "The grading system, {$grading->name}, has been restored");

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry, failed restoring the grading system");
            
        }
    }

    /**
     * Completely deleted a grading system from the database
     * 
     * @param mixed $gradingId
     */
    public function destroyGrading($gradingId)
    {
        try {

            /** @var Grading */
            $grading = Grading::where('id', $gradingId)->withTrashed()->firstOrFail();

            $this->authorize('forceDelete', $grading);

            $grading->forceDelete();

            session()->flash('status', "The grading system, {$grading->name}, has been completely deleted");

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry, failed completely deleting the grading system");
            
        }
    }
}
