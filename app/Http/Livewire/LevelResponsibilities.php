<?php

namespace App\Http\Livewire;

use App\Models\Level;
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection;

class LevelResponsibilities extends Component
{
    public Level $level;

    /**
     * Lifecycle first method
     * 
     * @param Level
     */
    public function mount(Level $level)
    {
        $this->level = $level;
    }

    public function render()
    {
        return view('livewire.level-responsibilities', [
            'responsibilities' => $this->getAllResponsibilities()
        ]);
    }

    /** @return Collection */
    public function getAllResponsibilities()
    {
        return $this->level->responsibilities()
            ->wherePivot('level_id', $this->level->id)
            ->get();
        
    }
}
