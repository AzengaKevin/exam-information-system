<?php

namespace App\Http\Livewire;

use App\Models\Grading;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Gradings extends Component
{

    public $gradingId;

    public $name;
    public $values = [];

    public function mount()
    {
        foreach (array_reverse(Grading::gradeOptions()) as $index => $value) {
            array_push($this->values, [
                "grade" => $value,
                "points" => $index + 1,
                "min" => null,
                "max" => null,
            ]);
        }
        
    }

    public function render()
    {
        return view('livewire.gradings', [
            'gradings' => $this->getAllGradings(),
            'grades' => Grading::gradeOptions()
        ]);
    }

    public function getAllGradings()
    {
        return Grading::all();
    }

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

    public function addGrading()
    {
        $data = $this->validate();
        
        try {
            $grading = Grading::create($data);

            if($grading){

                $this->reset(['name', 'values']);

                $this->resetValidation();

                session()->flash('status', 'Grading system Has been successfully created');

                $this->emit('hide-upsert-grading-modal');
            }

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', 'A fatal error occurred');

            $this->emit('hide-upsert-grading-modal');
            
        }
        
    }

    public function editGrading(Grading $grading)
    {
        $this->gradingId = $grading->id;

        $this->name = $grading->name;
        $this->values = $grading->values;

        $this->emit('show-upsert-grading-modal');
    }

    public function updateGrading()
    {
        $data = $this->validate();

        try {

            /** @var Grading */
            $grading = Grading::findOrFail($this->gradingId);

            if ($grading->update($data)) {

                $this->reset(['gradingId', 'name', 'values']);

                $this->resetValidation();

                session()->flash('status', 'Grading system Has been successfully updated');

                $this->emit('hide-upsert-grading-modal');
                
            }
            
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', 'A fatal error occurred');

            $this->emit('hide-upsert-grading-modal');
            
        }
    }

    public function showDeleteGradingModal(Grading $grading)
    {
        $this->gradingId = $grading->id;

        $this->name = $grading->name;

        $this->emit('show-delete-grading-modal');
    }

    public function deleteGrading()
    {

        try {

            /** @var Grading */
            $grading = Grading::findOrFail($this->gradingId);

            if ($grading->delete()) {

                $this->reset(['gradingId', 'name', 'values']);

                $this->resetValidation();

                session()->flash('status', 'Grading system Has been successfully deleted');

                $this->emit('hide-delete-grading-modal');
                
            }
            
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', 'A fatal error occurred');

            $this->emit('hide-delete-grading-modal');
            
        }
        
    }
}
