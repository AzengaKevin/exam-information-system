<?php

namespace App\Http\Livewire;

use App\Models\Level;
use App\Models\LevelUnit;
use App\Models\Stream;
use Livewire\Component;
use Livewire\WithPagination;

class LevelUnits extends Component
{

    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        return view('livewire.level-units',[
            'levelUnits' => $this->getPaginatedLevelUnits(),
            'levels' => $this->getLevels(),
            'streams' => $this->getStreams()
        ]);
    }

    public function getLevels()
    {
        return Level::orderBy('numeric')->get();
    }

    public function getStreams()
    {
        return Stream::orderBy('name')->get();
    }

    public function getPaginatedLevelUnits()
    {
        return LevelUnit::orderBy('alias')->paginate(24);
    }
}
