<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Department;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class Departments extends Component
{
    use AuthorizesRequests;

    public $departmentId;

    public $name;
    public $slug;
    public $description;

    public $trashed = false;

    /**
     * Lifecycle method that executes only once when the component is mounting
     * 
     * @param string trashed
     */
    public function mount(string $trashed = null)
    {
        $this->trashed = boolval($trashed);
    }

    /**
     * Lifecycle method that renders the component when the state of the component changes
     * 
     * @return View
     */
    public function render()
    {
        return view('livewire.departments', ['departments' => $this->getAppropriateDepartments()]);
    }

    /**
     * Get paginated departments from the database
     * 
     * @return Collection
     */
    public function getAppropriateDepartments()
    {
        $deparmentQuery = Department::with(['subjects']);

        if($this->trashed) $deparmentQuery->onlyTrashed();

        return $deparmentQuery->get();
    }

    /**
     * Show upsert user modal for editing and updating user
     * 
     * @param Department $department
     */
    public function editDepartment(Department $department)
    {
        $this->departmentId = $department->id;

        $this->name = $department->name;
        $this->description = $department->description;

        $this->emit('show-upsert-department-modal');
    }

    /**
     * Departments fields validation rules
     * 
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['bail', 'required', 'string', Rule::unique('departments')->ignore($this->departmentId)],
            'description' => ['bail', 'nullable']
        ];
    }

    /**
     * Create a department enrty to the database
     */
    public function createDepartment()
    {
        $data = $this->validate();
        
        try {

            $this->authorize('create', Department::class);

            $department = Department::create($data);

            session()->flash('status', "The department, {$department->name}, successfully created");

            $this->emit('hide-upsert-department-modal');
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $this->setError($exception, "Sorry! Department creation failed, if it persists consult the admin");

            $this->emit('hide-upsert-department-modal');

        }

    }

    /**
     * Set appropriate error based on the type of the erro and the environment
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
     * Update a department database entry
     */
    public function updateDepartment()
    {
        $data = $this->validate();

        try {

            /** @var User */
            $department = Department::findOrFail($this->departmentId);

            $this->authorize('update', $department);

            if($department->update($data)){

                session()->flash('status', "Department, {$department->fresh()->name} successfully updated");

                $this->emit('hide-upsert-department-modal');
            }
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $this->setError($exception, "Sorry! Upaditing department action failed");

            $this->emit('hide-upsert-department-modal');

        }
    }

    /**
     * Show confirmation modal for deleting a department
     * 
     * @param Department $department
     */
    public function showDeleteDepartmentModal(Department $department)
    {
        $this->departmtneId = $department->id;

        $this->name = $department->name;

        $this->emit('show-delete-department-modal');
        
    }

    /**
     * Trash a department entry
     */
    public function deleteDepartment()
    {
        try {

            /** @var Department */
            $department = Department::findOrFail($this->departmtneId);

            $this->authorize('delete', $department);

            if($department->delete()){

                $this->reset(['departmentId', 'name']);

                session()->flash('status', 'The department has been successfully deleted');

                $this->emit('hide-delete-department-modal');
            }

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry! The deleting department action failed");

            $this->emit('hide-delete-department-modal');
        }
    }

    /**
     * Restore a trashed department
     * 
     * @param mixed $departmentId
     */
    public function restoreDepartment($departmentId)
    {
        try {

            /** @var Department */
            $department = Department::where('id', $departmentId)->withTrashed()->firstOrFail();

            $this->authorize('restore', $department);

            $department->restore();

            session()->flash('status', "The department, {$department->name}, has been restored");

            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry! The restoring department action failed");
            
        }
        
    }

    /**
     * Completely delete a department from the database
     * 
     * @param mixed $departmentId
     */
    public function destroyDepartment($departmentId)
    {
        try {

            /** @var Department */
            $department = Department::where('id', $departmentId)->withTrashed()->firstOrFail();

            $this->authorize('forceDelete', $department);

            $department->forceDelete();

            session()->flash('status', "The department, {$department->name}, has been deleted from the system");

            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            $this->setError($exception, "Sorry! The destroying department action failed");
            
        }
        
    }
}
