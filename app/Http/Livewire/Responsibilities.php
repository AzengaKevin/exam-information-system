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
use Illuminate\Support\Facades\Gate;

class Responsibilities extends Component
{
    use WithPagination, AuthorizesRequests;

    protected $paginationTheme = 'bootstrap';

    public $responsibilityId;

    public $name;
    public $requirements = [];
    public $description;

    public function render()
    {
        return view('livewire.responsibilities', [
            'responsibilities' => $this->getResponsibilities(),
            'requirementOptions' => Responsibility::requirementOptions()
        ]);
    }

    /**
     * Get all database responsibilities (with teachers loaded)
     * 
     * @return Collection
     */
    public function getResponsibilities()
    {
        return Responsibility::with('teachers')->get();
    }

    /**
     * Show upsert user modal for editing and updating user
     * 
     * @param User $user
     */
    public function editResponsibility(Responsibility $responsibility)
    {
        
        $this->responsibilityId = $responsibility->id;

        $this->name = $responsibility->name;

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

    public function showDeleteResponsibilityModal(Responsibility $responsibility)
    {
        $this->departmentId = $responsibility->id;

        $this->name = $responsibility->name;

        $this->emit('show-delete-responsibility-modal');
        
    }

    public function deleteResponsibility(Responsibility $responsibility)
    {
        try {

            $responsibility = Responsibility::findOrFail($this->departmentId);

            if($responsibility->delete()){

                $this->reset(['responsibilityId', 'name']);

                session()->flash('status', 'The responsibility has been successfully deleted');

                $this->emit('hide-delete-responsibility-modal');
            }

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'responsibility-id' => $this->departmentId,
                'action' => __METHOD__
            ]);

            session()->flash('error', 'A fatal error has occurred');

            $this->emit('hide-delete-responsibility-modal');
        }
    }
}
