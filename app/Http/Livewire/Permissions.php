<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class Permissions extends Component
{
    use WithPagination, AuthorizesRequests;

    protected $paginationTheme = 'bootstrap';

    public $trashed = false;

    public $permissionId;

    public $name;
    public $slug;
    public $locked;
    public $description;
    public User $user;

    /**
     * Lifecycle method that executes only once when the component is mounting
     * 
     * @param string $trashed
     */
    public function mount(string $trashed = null)
    {
        $this->trashed = boolval($trashed);
        $this->user = Auth::user();
    }

    /**
     * Lifecycle method that renders the component when its state changes
     * 
     * @return View
     */
    public function render()
    {
        return view('livewire.permissions', ['permissions' => $this->getPaginatedPermissions()]);
    }

    /**
     * Get paginated permissions from the database
     * 
     * @return Paginator
     */
    public function getPaginatedPermissions()
    {
        $query = Permission::query();

        if(!$this->user->isSuperAdmin()) $query->unLocked();

        if($this->trashed) $query->onlyTrashed();

        return $query->paginate(24)->withQueryString();
    }

    /**
     * Show upsert user modal for editing and updating user
     * 
     * @param User $user
     */
    public function editPermission(Permission $permission)
    {
        
        $this->permissionId = $permission->id;

        $this->name = $permission->name;
        $this->locked = $permission->locked;
        $this->description = $permission->description;

        $this->emit('show-upsert-permission-modal');
    }

    /**
     * Validation rule for updating or creating role fields
     * 
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['bail', 'required', 'string', Rule::unique('permissions')->ignore($this->permissionId)],
            'locked' => ['bail', 'nullable'],
            'description' => ['bail', 'nullable']
        ];
    }

    /**
     * Persists a permission to the database
     */
    function createPermission()
    {
        $data = $this->validate();
        
        try {

            $this->authorize('create', Permission::class);

            $permission = Permission::create($data);

            if($permission){

                $this->reset(['name', 'description']);

                session()->flash('status', "A permission, {$permission->name}, has been successfully added");

                $this->emit('hide-upsert-permission-modal');
            }
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Failed to add the permission";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();

            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);

            $this->emit('hide-upsert-permission-modal');

        }
    }

    /**
     * Update permissions record in the database
     */
    public function updatePermission()
    {
        $data = $this->validate();

        try {

            /** @var Permission */
            $permission = Permission::findOrFail($this->permissionId);

            $this->authorize('update', $permission);

            if($permission->update($data)){

                $this->reset(['permissionId', 'name', 'description']);

                session()->flash('status', "The permission, {$permission->name}, successfully added");

                $this->emit('hide-upsert-permission-modal');
            }
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Failed to add the permission";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();

            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);

            $this->emit('hide-upsert-permission-modal');

        }
    }

    /**
     * Toggle the locked permissions status to the opposite of th current valude
     * 
     * @param Permission $permission
     */
    public function togglePermissionLockedStatus(Permission $permission)
    {
        try {
            $this->authorize('updateLocked', $permission);

            $permission->update(['locked' => $status = !$permission->fresh()->locked]);

            $result = $status ? "Locked" : "Unlocked";

            session()->flash('status', "{$permission->name} has been $result");
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Failed to add the permission";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();

            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);
            
        }

        
    }

    /**
     * Show the confirmation modal for deleting a permission
     * 
     * @param Permission $permission
     */
    public function showDeletePermissionModal(Permission $permission)
    {
        $this->permissionId = $permission->id;

        $this->name = $permission->name;

        $this->emit('show-delete-permission-modal');
        
    }

    /**
     * Trash a permission
     */
    public function deletePermission()
    {
        try {

            $permission = Permission::findOrFail($this->permissionId);

            $this->authorize('delete', $permission);

            if($permission->delete()){

                $this->reset(['permissionId', 'name']);

                session()->flash('status', 'The permission has been successfully deleted');

                $this->emit('hide-delete-permission-modal');
            }

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Failed to add the permission";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();

            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);

            $this->emit('hide-delete-permission-modal');
        }
    }

    /**
     * Restore a trashed permission
     * 
     * @param mixed $permissionId
     */
    public function restorePermission($permissionId)
    {
        try {

            /** @var Permission */
            $permission = Permission::where('id', $permissionId)->withTrashed()->firstOrFail();

            $this->authorize('restore', $permission);

            if($permission->restore()) session()->flash('status', "{$permission->name}, permission has been restores");

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Failed to add the permission";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();

            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);
            
        }
        
    }

    /**
     * Delete the permission entry from the database completely
     * 
     * @param mixed $permissionId
     */
    public function destroyPermission($permissionId)
    {
        try {
            
            /** @var Permission */
            $permission = Permission::where('id', $permissionId)->withTrashed()->firstOrFail();

            $this->authorize('forceDelete', $permission);

            if($permission->forceDelete()) session()->flash('status', "The permission, {$permission->name}, has been completely removed");

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Failed to add the permission";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();

            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);
            
        }
    }
}
