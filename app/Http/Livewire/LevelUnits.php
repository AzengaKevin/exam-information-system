<?php

namespace App\Http\Livewire;

use App\Models\Level;
use App\Models\Stream;
use Livewire\Component;
use App\Models\LevelUnit;
use Livewire\WithPagination;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LevelUnits extends Component
{
    use WithPagination, AuthorizesRequests;

    protected $paginationTheme = 'bootstrap';

    public $levels;
    public $streams;

    public $levelUnitId;

    public $level_id;
    public $stream_id;
    public $alias;
    public $description;

    public $trashed = false;

    /**
     * Component life cycle method that executes only once when the it is mounting
     * 
     * @param string $trashed
     */
    public function mount(string $trashed = null)
    {
        $this->trashed = boolval($trashed);
        
        $this->levels = $this->getLevels();

        $this->streams = $this->getStreams();
    }

    /**
     * Component Lifecycle method that executes everytime the internal state of the component changes
     * 
     * @return View
     */
    public function render()
    {
        return view('livewire.level-units',[
            'levelUnits' => $this->getPaginatedLevelUnits()
        ]);
    }

    /**
     * Get all levels from the database
     * 
     * @return Collection
     */
    public function getLevels()
    {
        return Level::orderBy('numeric')->get();
    }

    /**
     * Get all streams from the database
     * 
     * @return Collection
     */
    public function getStreams()
    {
        return Stream::orderBy('name')->get();
    }

    /**
     * Get paginated classes from the database
     * 
     * @return Paginator
     */
    public function getPaginatedLevelUnits()
    {
        $levelUnitQuery = LevelUnit::with(['students', 'responsibilities']);

        if($this->trashed) $levelUnitQuery->onlyTrashed();

        return $levelUnitQuery->orderBy('alias')->paginate(24);
    }

    /**
     * Component models fields validation rules
     * 
     * @return array
     */
    public function rules()
    {
        return [
            'level_id' => ['bail', 'required', 'integer'],
            'stream_id' => ['bail', 'required', 'integer'],
            'alias' => ['required', 'string'],
            'description' => ['required']
        ];
    }

    /**
     * Creates a new level unit entry in the database
     */
    public function addLevelUnit()
    {
        $data = $this->validate();
        
        try {

            $this->authorize('create', LevelUnit::class);

            $maybeLevelUnit = LevelUnit::where([
                ['level_id', $data['level_id']],
                ['stream_id', $data['stream_id'] ?? null]
            ])->first();

            if(is_null($maybeLevelUnit)){

                //level
                $level = Level::where('id',$this->level_id)->first();
                
                //stream
                $stream = Stream::where('id',$this->stream_id)->first();

                $data['alias'] = "{$level->numeric}{$stream->alias}";

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

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry! Creating class operation failed, contact the admin if this persists");

            $this->emit('hide-upsert-level-unit-modal');
            
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
     * Show thw upsert modal for editing a level unit
     * 
     * @param LevelUnit $levelUnit
     */
    public function editLevelUnit(LevelUnit $levelUnit)
    {
        $this->levelUnitId = $levelUnit->id;

        $this->level_id = $levelUnit->level_id;
        $this->stream_id = $levelUnit->stream_id;
        $this->alias = $levelUnit->alias;
        $this->description = $levelUnit->description;

        $this->emit('show-upsert-level-unit-modal');
    }

    /**
     * Update a level unit database entry
     */
    public function updateLevelUnit()
    {
        $data = $this->validate();
        
        try {

            /** @var LevelUnit */
            $levelUnit = LevelUnit::findOrFail($this->levelUnitId);

            $this->authorize('update', $levelUnit);

            $maybeLevelUnit = LevelUnit::where([
                ['id', '<>', $this->levelUnitId],
                ['level_id', $data['level_id']],
                ['stream_id', $data['stream_id'] ?? null]
            ])->first();

            if(is_null($maybeLevelUnit)){

                if($levelUnit->update($data)){

                    $this->reset();

                    session()->flash('status', 'A Level Unit Successfully Updated');

                    $this->emit('hide-upsert-level-unit-modal');
                }

            }else{

                session()->flash('error', 'A similar level unit exists');

                $this->emit('hide-upsert-level-unit-modal');

            }

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry! Updating class operation failed, contact the admin if this persists");

            $this->emit('hide-upsert-level-unit-modal');
            
        }
    }

    /**
     * Show confirmation modal for deleting a level
     * 
     * @param LevelUnit $levelUnit
     */
    public function showDeleteLevelUnitModal(LevelUnit $levelUnit)
    {
        $this->levelUnitId = $levelUnit->id;

        $this->alias = $levelUnit->alias;

        $this->emit('show-delete-level-unit-modal');
    }

    /**
     * Trash a level unit entry
     */
    public function deleteLevelUnit()
    {
        try {

            /** @var LevelUnit */
            $levelUnit = LevelUnit::findOrFail($this->levelUnitId);

            $this->authorize('delete', $levelUnit);

            if($levelUnit->delete()){

                $this->reset();

                $this->resetPage();

                session()->flash('status', 'A Level Unit Successfully Deleted');

                $this->emit('hide-delete-level-unit-modal');
            }

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry! Deleting class operation failed, contact the admin if this persists");

            $this->emit('hide-delete-level-unit-modal');
            
        }
    }

    /**
     * Restore a trashed level unit
     * 
     * @param mixed $levelUnitId
     */
    public function restoreLevelUnit($levelUnitId)
    {
       try {
        
            /** @var LevelUnit */
            $levelUnit = LevelUnit::where('id', $levelUnitId)->withTrashed()->firstOrFail();

            $this->authorize('restore', $levelUnit);

            $levelUnit->restore();

            session()->flash('status', "{$levelUnit->alias} has been restored");

       } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry! Restoring class operation failed, contact the admin if this persists");

       } 
    }

    /**
     * Completely delete a level unit from the application
     * 
     * @param mixed $levelUnitId
     */
    public function destroyLevelUnit($levelUnitId)
    {
        try {
         
             /** @var LevelUnit */
             $levelUnit = LevelUnit::where('id', $levelUnitId)->withTrashed()->firstOrFail();
 
             $this->authorize('forceDelete', $levelUnit);
 
             $levelUnit->forceDelete();
 
             session()->flash('status', "{$levelUnit->alias} has been completely deleted");
 
        } catch (\Exception $exception) {
 
            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry! Deleting class operation failed, contact the admin if this persists");
 
        } 
        
    }
}
