<?php

namespace App\Http\Livewire;

use App\Models\Level;
use Livewire\Component;

class LevelStudents extends Component
{
    public Level $level;
    /**
     * The method called when the component is mounting and occurs once in the
     * lifecycle of this component
     * 
     * @param Level $level
     */
    public function mount(Level $level)
    {
        $this->level = $level;
        
    }

    /**
     * Renders and re-renders anytime the component state changes
     */
    public function render()
    {
        return view('livewire.level-students', [
            'students' => $this->getPaginatedStudentsInTheCurrentLevel()
        ]);
    }

    /**
     * Get students to show in the view
     * 
     * @return Paginator
     */
    public function getPaginatedStudentsInTheCurrentLevel()
    {
        return $this->level->fresh()->students()->orderBy('name')
            ->paginate(24)->withQueryString();
        
    }
}
