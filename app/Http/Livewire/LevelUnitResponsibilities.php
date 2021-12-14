<?php

namespace App\Http\Livewire;

use App\Models\LevelUnit;
use Livewire\Component;

class LevelUnitResponsibilities extends Component
{

    public LevelUnit $levelUnit;

    public function mount(LevelUnit $levelUnit)
    {
        $this->levelUnit = $levelUnit;
    }

    public function render()
    {
        return view('livewire.level-unit-responsibilities', [
            'responsibilities' => $this->getAllResponsibilities()
        ]);
    }

    /**
     * Get all level unit responsibility
     * 
     */
    public function getAllResponsibilities()
    {
        return $this->levelUnit->responsibilities;
    }
}
