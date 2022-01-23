<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Responsibility;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\App;

class Responsibilities extends Component
{
    use WithPagination, AuthorizesRequests;

    protected $paginationTheme = 'bootstrap';

    public $responsibilityId;

    public $name;
    public $how_many;
    public $requirements = [];
    public $description;

    public $trashed = false;

    /**
     * Component lifecycle method that executes once when the component
     * is mounting
     * 
     * @param string $trashed
     */
    public function mount(string $trashed = null)
    {
        $this->trashed = boolval($trashed);
    }

    /**
     * Component life cycle method that executes when the state of the
     * component changes
     * 
     * @return View
     */
    public function render()
    {
        return view('livewire.responsibilities', [
            'responsibilities' => $this->getPaginatedResponsibilities(),
            'requirementOptions' => Responsibility::requirementOptions()
        ]);
    }

    /**
     * Get all database responsibilities (with teachers loaded)
     * 
     * @return Collection
     */
    public function getPaginatedResponsibilities()
    {
        $responsibilitiesQuery = Responsibility::with(['teachers']);

        if($this->trashed) $responsibilitiesQuery->onlyTrashed();

        return $responsibilitiesQuery->paginate(24);
    }

    /**
     * Show upsert user modal for editing and updating user
     * 
     * @param Responsibility $responsibility
     */
    public function editResponsibility(Responsibility $responsibility)
    {
        
        $this->responsibilityId = $responsibility->id;

        $this->name = $responsibility->name;

        $this->how_many = $responsibility->how_many;

        $this->requirements = $responsibility->requirements;

        $this->description = $responsibility->description;

        $this->emit('show-upsert-responsibility-modal');
    }

    /**
     * Resonsibilities properties validation rules
     * 
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['bail', 'required', 'string', Rule::unique('responsibilities')->ignore($this->responsibilityId)],
            'how_many' => ['bail', 'nullable', 'integer'],
            'requirements' => ['nullable', 'array', Rule::in(Responsibility::requirementOptions())],
            'description' => ['bail', 'nullable']
        ];
    }

    /**
     * Persists a new responsibility to the database
     */
    public function createResponsibility()
    {
        $data = $this->validate();
        
        try {

            $this->authorize('create', Responsibility::class);

            $responsibility = Responsibility::create($data);

            $this->reset(['name', 'requirements', 'description']);

            session()->flash('status', "Responsibility, {$responsibility->name} successfully created");

            $this->emit('hide-upsert-responsibility-modal');
            
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Failed to create the responsibility";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();

            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);
            
            $this->emit('hide-upsert-responsibility-modal');

        }
    }

    /**
     * Update a database responsibility enrty
     */
    public function updateResponsibility()
    {
        $data = $this->validate();

        try {

            /** @var Responsibility */
            $responsibility = Responsibility::findOrFail($this->responsibilityId);

            $this->authorize('update', $responsibility);

            if ($responsibility->locked) $this->authorize('updateLocked', $responsibility);

            if($responsibility->update($data)){

                session()->flash('status', 'responsibility successfully updated');

                $this->emit('hide-upsert-responsibility-modal');
            }
            
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Failed to update the responsibility";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();

            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);
            
            $this->emit('hide-upsert-responsibility-modal');

        }
    }

    /**
     * Toggle the responsibility Locked Status
     * 
     * @param Responsiblity $responsibility
     */
    public function toggleResponsibilityLock(Responsibility $responsibility)
    {
        try {

            $this->authorize('updateLocked', $responsibility);

            $responsibility->update(['locked' => !$responsibility->locked]);
            

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Failed to update the responsibility";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();

            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);
                        
        }
        
    }

    /**
     * Show the confirmation modal for deleting responsibility
     * 
     * @param Responsibilitiy $responsibility
     */
    public function showDeleteResponsibilityModal(Responsibility $responsibility)
    {
        $this->departmentId = $responsibility->id;

        $this->name = $responsibility->name;

        $this->emit('show-delete-responsibility-modal');
        
    }

    /**
     * Trash a responsibility
     */
    public function deleteResponsibility()
    {
        try {

            /** @var Responsibility */
            $responsibility = Responsibility::findOrFail($this->departmentId);

            $this->authorize('delete', $responsibility);

            if($responsibility->delete()){

                $this->reset(['responsibilityId', 'name']);

                session()->flash('status', 'The responsibility has been successfully deleted');

                $this->emit('hide-delete-responsibility-modal');
            }

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Failed to update the responsibility";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();

            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);

            $this->emit('hide-delete-responsibility-modal');
        }
    }

    /** 
     * Restore a trashed responsibilty
     * 
     * @param mixed $responsibilityId
     */
    public function restoreResponsibility($responsibilityId)
    {
        try {

            /** @var Responsibility */
            $responsibility = Responsibility::where('id', $responsibilityId)->withTrashed()->firstOrFail();
            
            $this->authorize('restore', $responsibility);

            $responsibility->restore();

            session()->flash('status', "The responsibility, {$responsibility->name}, has been restored");

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Failed to update the responsibility";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();

            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);
            
        }
    }

    /**
     * Completely delete a responsibility from the database
     * 
     * @param mixed $responsibilityId
     */
    public function destroyResponsibility($responsibilityId)
    {
        try {

            /** @var Responsibility */
            $responsibility = Responsibility::where('id', $responsibilityId)->withTrashed()->firstOrFail();
            
            $this->authorize('forceDelete', $responsibility);

            $responsibility->forceDelete();

            session()->flash('status', "The responsibility, {$responsibility->name}, has been completely deletd");
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Failed to update the responsibility";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();

            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);
            
        }
    }
}
