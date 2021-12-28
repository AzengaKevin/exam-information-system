<?php

namespace App\Http\Livewire;

use App\Models\Stream;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class Streams extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $streamId;

    public $name;
    public $alias;
    public $slug;
    public $description;

    public function render()
    {
        return view('livewire.streams', [
            'streams' => $this->getPaginatedLevels()
        ]);
    }

    public function getPaginatedLevels()
    {
        return Stream::paginate(24);
    }

    /**
     * Show upsert user modal for editing and updating user
     * 
     * @param User $user
     */
    public function editStream(Stream $stream)
    {
        
        $this->streamId = $stream->id;

        $this->name = $stream->name;
        $this->alias = $stream->alias;
        $this->description = $stream->description;

        $this->emit('show-upsert-stream-modal');
    }

    public function rules()
    {
        return [
            'name' => ['bail', 'required', 'string', Rule::unique('streams')->ignore($this->streamId)],
            'alias' => ['bail','required','string'],
            'description' => ['bail', 'nullable']
        ];
    }

    function createStream()
    {
        $this->validate();
        
        try {

            Stream::create([
                'name'=>$this->name,
               'alias'=> $this->alias,
                'description'=>$this->description ,
                'slug'=>Str::slug($this->name)
            ]);
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'user-id' => $this->userId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

        }
        $this->emit('hide-upsert-stream-modal');
    }


    public function updateStream()
    {
        $data = $this->validate();

        try {

            /** @var User */
            $stream = Stream::findOrFail($this->streamId);

            if($stream->update($data)){

                session()->flash('status', 'stream successfully updated');

                $this->emit('hide-upsert-stream-modal');
            }
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'user-id' => $this->userId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

        }
    }

    public function showDeleteStreamModal(Stream $stream)
    {
        $this->streamId = $stream->id;

        $this->name = $stream->name;

        $this->emit('show-delete-stream-modal');
        
    }

    public function deleteLevel(Stream $stream)
    {
        try {

            $stream = Stream::findOrFail($this->streamId);

            if($stream->delete()){

                $this->reset(['streamId', 'name']);

                session()->flash('status', 'The stream has been successfully deleted');

                $this->emit('hide-delete-stream-modal');
            }

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'stream-id' => $this->streamId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

            session()->flash('error', 'A fatal error has occurred');

            $this->emit('hide-delete-stream-modal');
        }
    }

    /** 
     * Deleting all current available streams from the database
     */
    public function truncateStreams()
    {
        try {

            /** @var Collection */
            $streams = Stream::all();

            $streams->each(function(Stream $stream){
                $stream->forceDelete();
            });

            session()->flash('status', 'All system streams have been deleted');

            $this->emit('hide-truncate-streams-modal');
            
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);
            
            session()->flash('error', 'An error occurred while deleting systems streams');

            $this->emit('hide-truncate-streams-modal');
        }
    }
}
