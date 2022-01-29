<?php

namespace App\Http\Livewire;

use App\Models\Level;
use App\Models\Subject;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class Levels extends Component
{
    use WithPagination, AuthorizesRequests;

    protected $paginationTheme = 'bootstrap';

    public $levelId;

    public $name;
    public $numeric;
    public $slug;
    public $description;
    public $selectedOptionalSubjects = array();

    public $trashed = false;

    public $optionalSubjects;

    /**
     * Lifecycle method that gets called onces when the component is mountint
     * 
     * @param string $trashed
     */
    public function mount(string $trashed = null)
    {
        $this->trashed = boolval($trashed);
        $this->optionalSubjects = $this->getAllOptionalSubjects();
    }

    /**
     * Lifecycle method the renders the component everytime the state of the component changes
     * 
     * @return View
     */
    public function render()
    {
        return view('livewire.levels', ['levels' => $this->getPaginatedLevels()]);
    }

    /**
     * Get all levels from the database
     * 
     * @return Paginator
     */
    public function getPaginatedLevels()
    {
        $levelsQuery = Level::with(['students']);

        if($this->trashed) $levelsQuery->onlyTrashed();

        return $levelsQuery->paginate(24)->withQueryString();
    }

    /**
     * Get all optional subjects from the database
     * 
     * @return Collection
     */
    public function getAllOptionalSubjects()
    {
        return Subject::optional()->get(['id', 'name']);
    }
    
   
    /**
     * Show upsert user modal for editing and updating user
     * 
     * @param Level $level
     */
    public function editLevel(Level $level)
    {
        
        $this->levelId = $level->id;

        $this->name = $level->name;
        $this->numeric = $level->numeric;
        $this->description = $level->description;
        
        $subjectsIds = $level->optionalSubjects->pluck('id')->all();

        $this->selectedOptionalSubjects = array_fill_keys($subjectsIds, 'true');

        $this->emit('show-upsert-level-modal');
    }

    /**
     * Define the component fields validation rules
     * 
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['bail', 'required', 'string', Rule::unique('levels')->ignore($this->levelId)],
            'numeric' => ['bail','required','integer'],
            'description' => ['bail', 'nullable'],
            'selectedOptionalSubjects' => ['nullable', 'array']
        ];
    }

    /**
     * Creates a new level record in the database based on user input
     */
    public function createLevel()
    {
        $data = $this->validate();
        
        try {

            $this->authorize('create', Level::class);

            $data['selectedOptionalSubjects'] = array_filter(
                $data['selectedOptionalSubjects'],
                fn($value, $key) => boolval($value),
                ARRAY_FILTER_USE_BOTH
            );

            DB::transaction(function() use($data){

                /** @var Level */
                $level = Level::create($data);

                $level->optionalSubjects()->sync(array_keys($data['selectedOptionalSubjects']));

                $this->reset(['name', 'numeric', 'description', 'slug']);
    
                session()->flash('status', 'Level successfully added');
        
                $this->emit('hide-upsert-level-modal');

            });

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry! Level addition action failed");
        
            $this->emit('hide-upsert-level-modal');

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
     * Updating the current database level record
     */
    public function updateLevel()
    {
        $data = $this->validate();

        try {

            $data['selectedOptionalSubjects'] = array_filter(
                $data['selectedOptionalSubjects'],
                fn($value, $key) => boolval($value),
                ARRAY_FILTER_USE_BOTH
            );

            /** @var Level */
            $level = Level::findOrFail($this->levelId);

            $this->authorize('update', $level);

            DB::transaction(function() use($level, $data){

                $level->update($data);

                $level->optionalSubjects()->sync(array_keys($data['selectedOptionalSubjects']));

                $this->reset(['levelId', 'name', 'numeric', 'slug', 'description']);

                session()->flash('status', 'Level successfully updated');

                $this->emit('hide-upsert-level-modal');

            });
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry! Level updating action failed");
        
            $this->emit('hide-upsert-level-modal');

        }
    }

    /**
     * Shows the modal for deleting a specified level
     * 
     * @param Level $level
     */
    public function showDeleteLevelModal(Level $level)
    {
        $this->levelId = $level->id;

        $this->name = $level->name;

        $this->emit('show-delete-level-modal');
        
    }

    /**
     * Trash a level
     */
    public function deleteLevel()
    {
        try {

            /** @var Level */
            $level = Level::findOrFail($this->levelId);

            $this->authorize('delete', $level);

            if($level->delete()){

                $this->reset(['levelId', 'name']);

                session()->flash('status', "The level, {$level->name}, has been successfully deleted");

                $this->emit('hide-delete-level-modal');
            }

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry! Level addition action failed");

            $this->emit('hide-delete-level-modal');
        }
    }

    /**
     * Delete all levels from the database
     */
    public function truncateLevels()
    {
        try {

            $this->authorize('bulkDelete', Level::class);

            /** @var Collection */
            $levels = Level::all();

            $levels->each(function(Level $level){
                $level->delete();
            });

            session()->flash('status', 'All system levels have been deleted');

            $this->emit('hide-truncate-levels-modal');
            
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry! Level truncation action failed");

            $this->emit('hide-truncate-levels-modal');
        }
    }

    /**
     * Restore a trashed level
     * 
     * @param mixed $levelId
     * 
     */
    public function restoreLevel($levelId)
    {
        try {
            
            /** @var Level */
            $level = Level::where('id', $levelId)->withTrashed()->firstOrFail();

            $this->authorize('restore', $level);

            $level->restore();

            session()->flash('status', "The level, {$level->name}, has been restored");

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry! Level restoring action failed");
            
        }
    }

    /**
     * Completely delete a level from the database
     * 
     * @param mixed $levelId
     */
    public function destroyLevel($levelId)
    {
        try {
            
            /** @var Level */
            $level = Level::where('id', $levelId)->withTrashed()->firstOrFail();

            $this->authorize('forceDelete', $level);

            $level->forceDelete();

            session()->flash('status', "The level, {$level->name}, has been completely deleted from the system");

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry! Level destroying action failed");
            
        }
    }
}
