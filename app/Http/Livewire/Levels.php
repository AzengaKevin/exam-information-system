<?php

namespace App\Http\Livewire;

use App\Models\Level;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class Levels extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $levelId;

    public $name;
    public $numeric;
    public $slug;
    public $description;

    public function render()
    {
        return view('livewire.levels', [
            'levels' => $this->getPaginatedLevels()
        ]);
    }

    public function getPaginatedLevels()
    {
        return Level::paginate(24);
    }

    
   
    /**
     * Show upsert user modal for editing and updating user
     * 
     * @param User $user
     */
    public function editLevel(Level $level)
    {
        
        $this->levelId = $level->id;

        $this->name = $level->name;
        $this->numeric = $level->numeric;
        $this->description = $level->description;

        $this->emit('show-upsert-level-modal');
    }

    public function rules()
    {
        return [
            'name' => ['bail', 'required', 'string'],
            'numeric' => ['bail','required','integer'],
            'description' => ['bail', 'nullable']
        ];
    }

    function createLevel()
    {
        $this->validate();
        
        try {

            Level::create([
                'name'=>$this->name,
               'numeric'=> $this->numeric,
                'description'=>$this->description ,
                'slug'=>Str::slug($this->name)
            ]);
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'user-id' => $this->userId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

        }
        $this->emit('hide-upsert-level-modal');
    }


    public function updateLevel()
    {
        $data = $this->validate();

        try {

            /** @var User */
            $level = Level::findOrFail($this->levelId);

            if($level->update($data)){

                session()->flash('status', 'level successfully updated');

                $this->emit('hide-upsert-level-modal');
            }
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'user-id' => $this->userId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

        }
    }

    public function showDeleteLevelModal(Level $level)
    {
        $this->levelId = $level->id;

        $this->name = $level->name;

        $this->emit('show-delete-level-modal');
        
    }

    public function deleteLevel(Level $level)
    {
        try {

            $level = Level::findOrFail($this->levelId);

            if($level->delete()){

                $this->reset(['levelId', 'name']);

                session()->flash('status', 'The level has been successfully deleted');

                $this->emit('hide-delete-level-modal');
            }

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'level-id' => $this->levelId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

            session()->flash('error', 'A fatal error has occurred');

            $this->emit('hide-delete-level-modal');
        }
    }

}
