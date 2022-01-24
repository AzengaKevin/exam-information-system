<?php

namespace App\Http\Livewire;

use App\Models\Hostel;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Hostels extends Component
{
    use AuthorizesRequests;

    public $hostelId;

    public $name;
    public $description;

    public $trashed = false;

    /**
     * Lifecycle method that executes only once when the component is mounting
     * 
     * @param string $trashed
     */
    public function mount(string $trashed = null)
    {
        $this->trashed = boolval($trashed);
    }

    /**
     * Lifecycle method that renders the component everytime the state of the component changes
     * 
     * @return View
     */
    public function render()
    {
        return view('livewire.hostels', ['hostels' => $this->getAllHostels()]);
    }

    /**
     * Get all hostels from the database
     * 
     * @return Collection
     */
    public function getAllHostels()
    {
        $hostelsQuery = Hostel::with(['students']);

        if($this->trashed) $hostelsQuery->onlyTrashed();

        return $hostelsQuery->get();
    }

    /**
     * Show upsert hostel modal for editing and updating hostel
     * 
     * @param Hostel $hostel
     */
    public function editHostel(Hostel $hostel)
    {
        
        $this->hostelId = $hostel->id;

        $this->name = $hostel->name;
        $this->description = $hostel->description;

        $this->emit('show-upsert-hostel-modal');
    }

    /**
     * Define component modelled properties validation rules
     * 
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['bail', 'required', 'string', Rule::unique('hostels')->ignore($this->hostelId)],
            'description' => ['bail', 'nullable']
        ];
    }

    /**
     * Create a new hostel entry in the database
     */
    function createHostel()
    {
        $data = $this->validate();
        
        try {

            $this->authorize('create', Hostel::class);

            /** @var Hostel */
            $hostel = Hostel::create($data);

            $this->resetValidation();

            $this->reset(['name', 'description']);
            
            session()->flash('status', "{$hostel->name} has been succefully added");

            $this->emit('hide-upsert-hostel-modal');
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $this->setError($exception, "Sorry, creating hostel operation failed");

            $this->emit('hide-upsert-hostel-modal');
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
     * Updates a database hostel entry
     */
    public function updateHostel()
    {
        $data = $this->validate();

        try {

            /** @var Hostel */
            $hostel = Hostel::findOrFail($this->hostelId);

            $this->authorize('update', $hostel);

            if($hostel->update($data)){

                $this->resetValidation();
    
                $this->reset(['hostelId', 'name', 'description']);

                session()->flash('status', 'hostel successfully updated');

                $this->emit('hide-upsert-hostel-modal');
            }
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry, updating hostel operation failed");

            $this->emit('hide-upsert-hostel-modal');

        }
    }

    /**
     * Show confirmation modal for deleting a hostel
     * 
     * @param Hostel $hostel
     */
    public function showDeleteHostelModal(Hostel $hostel)
    {
        $this->departmtneId = $hostel->id;

        $this->name = $hostel->name;

        $this->emit('show-delete-hostel-modal');
        
    }

    /**
     * Trash a hostel entry
     */
    public function deleteHostel()
    {
        try {

            /** @var Hostel */
            $hostel = Hostel::findOrFail($this->departmtneId);

            $this->authorize('delete', $hostel);

            if($hostel->delete()){

                $this->reset(['hostelId', 'name']);

                session()->flash('status', 'The hostel has been successfully deleted');

                $this->emit('hide-delete-hostel-modal');
            }

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry, deleting hostel operation failed");

            $this->emit('hide-delete-hostel-modal');
        }
    }

    /**
     * Restore a deleted hostel
     * 
     * @param mixed $hostelId
     */
    public function restoreHostel($hostelId)
    {
        try {
            
            /** @var Hostel */
            $hostel = Hostel::where('id', $hostelId)->withTrashed()->firstOrFail();

            $this->authorize('restore', $hostel);

            $hostel->restore();

            session()->flash('status', "The hostel, {$hostel->name}, has been restored");

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry, restoring hostel operation failed");
            
        }
    }

    /**
     * Completely delete a hostel from the database
     * 
     * @param mixed $hostelId
     */
    public function destroyHostel($hostelId)
    {
        try {
            
            /** @var Hostel */
            $hostel = Hostel::where('id', $hostelId)->withTrashed()->firstOrFail();

            $this->authorize('forceDelete', $hostel);

            $hostel->forceDelete();

            session()->flash('status', "The hostel, {$hostel->name}, has been deleted completely");

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry, deleting hostel operation failed");
            
        }
        
    }
}
