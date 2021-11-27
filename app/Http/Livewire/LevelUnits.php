<?php

namespace App\Http\Livewire;

use App\Models\Level;
use App\Models\LevelUnit;
use App\Models\Stream;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class LevelUnits extends Component
{

    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $levelUnitId;

    public $level_id;
    public $stream_id;
    public $alias;
    public $description;

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

    public function rules()
    {
        return [
            'level_id' => ['bail', 'required', 'integer'],
            'stream_id' => ['nullable', 'integer'],
            'alias' => ['nullable', 'string'],
            'description' => ['nullable']
        ];
    }

    public function addLevelUnit()
    {
        $data = $this->validate();
        
        try {

            $maybeLevelUnit = LevelUnit::where([
                ['level_id', $data['level_id']],
                ['stream_id', $data['stream_id'] ?? null]
            ])->first();

            if(is_null($maybeLevelUnit)){

                $levelUnit = LevelUnit::create($data);

                if($levelUnit){

                    $this->reset();

                    $this->resetPage();

                    session()->flash('status', 'A Level Unit Successfully Added');

                    $this->emit('hide-upsert-level-unit-modal');
                }

            }else{

                session()->flash('error', 'The level unit has already been added');

                $this->emit('hide-upsert-level-unit-modal');

            }

        } catch (\Exception $eaxception) {

            Log::error($eaxception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', 'A fatal error occurred, when adding level unit');

            $this->emit('hide-upsert-level-unit-modal');
            
        }
        
    }
}
