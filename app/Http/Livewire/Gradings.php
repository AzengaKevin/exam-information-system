<?php

namespace App\Http\Livewire;

use App\Models\Grading;
use Livewire\Component;

class Gradings extends Component
{
    public function render()
    {
        return view('livewire.gradings', [
            'gradings' => $this->getAllGradings(),
            'gradeOptions' => Grading::gradeOptions()
        ]);
    }

    public function getAllGradings()
    {
        return Grading::all();
    }
}
