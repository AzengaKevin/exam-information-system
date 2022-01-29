<?php

namespace App\Http\Livewire;

use App\Models\Stream;
use App\Services\SubjectService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class Streams extends Component
{
    use WithPagination, AuthorizesRequests;

    protected $paginationTheme = 'bootstrap';

    public $streamId;

    public $name;
    public $alias;
    public $description;

    public $trashed = false;

    public $optionalSubjects;
    public $selectedOptionalSubjects = array();

    /**
     * Lifecycle method that executes only once when the component is mounting
     * 
     * @param string $trashed
     * @param SubjectService $subjectService
     */
    public function mount(
        SubjectService $subjectService,
        string $trashed = null
    )
    {
        $this->optionalSubjects = $subjectService->getOptionalSubjects();
        $this->trashed = boolval($trashed);
    }

    /**
     * Lifecycle method method that executes everytime the component state changes
     * 
     * @return View
     */
    public function render()
    {
        return view('livewire.streams', ['streams' => $this->getRelevantStreams()]);
    }

    /**
     * Get relevant paginated streas
     */
    public function getRelevantStreams()
    {
        $streamsQuery = Stream::with('students');

        if($this->trashed) $streamsQuery->onlyTrashed();

        return $streamsQuery->paginate(24)->withQueryString();
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

        $this->selectedOptionalSubjects = array_fill_keys($stream->optionalSubjects->pluck('id')->all(), 'true');

        $this->emit('show-upsert-stream-modal');
    }

    /**
     * Streams fields validation rules
     * 
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['bail', 'required', 'string', Rule::unique('streams')->ignore($this->streamId)],
            'alias' => ['bail','required','string'],
            'selectedOptionalSubjects' => ['nullable', 'array'],
            'description' => ['bail', 'nullable']
        ];
    }

    /**
     * Creates a new stream database entry
     */
    public function createStream()
    {
        $data = $this->validate();
        
        try {

            $this->authorize('create', Stream::class);

            if(!empty($data['selectedOptionalSubjects'])){
                $data['selectedOptionalSubjects'] = array_filter(
                    $data['selectedOptionalSubjects'],
                    fn($value, $key) => boolval($value),
                    ARRAY_FILTER_USE_BOTH
                );
            }

            DB::transaction(function() use($data){
    
                /** @var Stream */
                $stream = Stream::create($data);

                $stream->optionalSubjects()->sync(array_keys($data['selectedOptionalSubjects']));
    
                $this->reset(['name', 'alias', 'description']);
    
                session()->flash('status', "A stream, {$stream->name}, has been successfully created");
    
                $this->emit('hide-upsert-stream-modal');
            });
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry! Adding stream operation failed");

            $this->emit('hide-upsert-stream-modal');

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
     * Updates a database stream entry
     */
    public function updateStream()
    {
        $data = $this->validate();

        try {

            /** @var Stream */
            $stream = Stream::findOrFail($this->streamId);

            $this->authorize('update', $stream);

            if(!empty($data['selectedOptionalSubjects'])){
                $data['selectedOptionalSubjects'] = array_filter(
                    $data['selectedOptionalSubjects'],
                    fn($value, $key) => boolval($value),
                    ARRAY_FILTER_USE_BOTH
                );
            }

            DB::transaction(function() use($stream, $data){

                $stream->update($data);

                $stream->optionalSubjects()->sync(array_keys($data['selectedOptionalSubjects']));
    
                $this->reset(['streamId', 'name', 'alias', 'description']);

                session()->flash('status', 'stream successfully updated');

                $this->emit('hide-upsert-stream-modal');
            });

            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry! Updating stream operation failed");

            $this->emit('hide-upsert-stream-modal');

        }
    }

    /**
     * Shows the delete streamm confrimation modal
     * 
     * @param Stream $stream
     */
    public function showDeleteStreamModal(Stream $stream)
    {
        $this->streamId = $stream->id;

        $this->name = $stream->name;

        $this->emit('show-delete-stream-modal');
        
    }

    /**
     * Trash a stream entry
     */
    public function deleteStream()
    {
        try {

            /** @var Stream */
            $stream = Stream::findOrFail($this->streamId);

            $this->authorize('delete', $stream);

            if($stream->delete()){

                $this->reset(['streamId', 'name']);

                session()->flash('status', "The stream, {$stream->name}, has been successfully deleted");

                $this->emit('hide-delete-stream-modal');
            }

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry! Deleting stream operation failed");

            $this->emit('hide-delete-stream-modal');
        }
    }

    /** 
     * Deleting all current available streams from the database
     */
    public function truncateStreams()
    {
        try {

            $this->authorize('bulkDelete', Stream::class);

            /** @var Collection */
            $streams = Stream::all();
            
            $streams->each(function(Stream $stream){
                $stream->delete();
            });

            session()->flash('status', 'All system streams have been deleted');

            $this->emit('hide-truncate-streams-modal');
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry! Truncating streams operation failed");

            $this->emit('hide-truncate-streams-modal');
        }
    }

    /**
     * A stream can restored
     * 
     * @param mixed $streamId
     */
    public function restoreStream($streamId)
    {

        try {
            
            /** @var Stream */
            $stream = Stream::where('id', $streamId)->withTrashed()->firstOrFail();

            $this->authorize('restore', $stream);

            $stream->restore();

            session()->flash('status', "The stream, {$stream->name}, has been restored");

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry! Restoring stream operation failed");
            
        }
        
    }

    /**
     * Completely delete a stream from the database
     * 
     * @param mixed $steamId
     */
    public function destroyStream($streamId)
    {

        try {
            
            /** @var Stream */
            $stream = Stream::where('id', $streamId)->withTrashed()->firstOrFail();

            $this->authorize('forceDelete', $stream);

            $stream->forceDelete();

            session()->flash('status', "The stream, {$stream->name}, has been completely deleted from the application");

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry! Deleting stream operation failed, check with admin if this persistes");
            
        }
        
    }
}
