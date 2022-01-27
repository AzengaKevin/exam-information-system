<?php

namespace App\Http\Livewire;

use App\Models\Role;
use App\Models\User;
use Livewire\Component;
use App\Models\Permission;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Roles extends Component
{
    use WithPagination, AuthorizesRequests;

    protected $paginationTheme = 'bootstrap';
    
    public $trashed = false;

    public $roleId;

    public $name;
    public $slug;
    public $description;

    public $selectedPermissions = [];

    public $permissions;

    public User $user;

    /**
     * Lifecycle method that executes once when the component is mounting
     * 
     * @return void
     */
    public function mount(string $trashed = null)
    {
        $this->trashed = boolval($trashed);

        $this->user = Auth::user();
        
        $this->permissions = $this->getAllPermissions();
    }

    /**
     * Lifecycle method that renders the component when the state on the component changes
     * 
     * @return View
     */
    public function render()
    {
        return view('livewire.roles', ['roles' => $this->getPaginatedRoles()]);
    }

    /**
     * Gets all paginated roles from the database
     * 
     * @return Paginator
     */
    public function getPaginatedRoles()
    {

        /** @var Builder */
        $rolesQuery = Role::query();

        if(!$this->user->isSuperAdmin()) $rolesQuery->visible();

        $rolesQuery->with(['permissions', 'users']);

        if ($this->trashed) $rolesQuery->onlyTrashed();

        return $rolesQuery->paginate(24)->withQueryString();
    }

    /**
     * Get all permissions from the database
     * 
     * @return Collection
     */
    public function getAllPermissions()
    {
        $query = Permission::query();

        if(!$this->user->isSuperAdmin()) $query->unLocked();

        if($this->trashed) $query->onlyTrashed();

        return $query->get(['id', 'name']);
    }

    /**
     * Show upsert user modal for editing and updating user
     * 
     * @param Role $role
     */
    public function editRole(Role $role)
    {
        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->description = $role->description;

        $this->emit('show-upsert-role-modal');
    }

    /**
     * Validation rules for adding a role
     * 
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['bail', 'required', 'string'],
            'description' => ['bail', 'nullable']
        ];
    }

    /**
     * Persists a new role to the database
     */
    function createRole()
    {
        $this->validate();
        
        try {

            $this->authorize('create', Role::class);
                
            $role = Role::create([
                'name'=>$this->name,
                'description'=>$this->description,
                'slug'=>Str::slug($this->name)
            ]);

            if($role){

                $this->reset(['name', 'description']);

                session()->flash('status', 'A new role has been successfully added');
    
                $this->emit('hide-upsert-role-modal');
            }
        
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Failed to add the new role";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();

            else $message = App::environment('local') ? $exception->getMessage() : $message;
            
            session()->flash('error', $message);

            $this->emit('hide-upsert-role-modal');
        }
    }

    /**
     * Updates a database role entry
     */
    public function updateRole()
    {
        $data = $this->validate();

        try {

            /** @var User */
            $role = Role::findOrFail($this->roleId);

            $this->authorize('update', $role);

            if($role->update($data)){

                $this->reset(['roleId', 'name', 'description']);

                session()->flash('status', "The role, {$role->name}, successfully updated");

                $this->emit('hide-upsert-role-modal');
            }
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Failed to update the role";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();

            else $message = App::environment('local') ? $exception->getMessage() : $message;
            
            session()->flash('error', $message);

            $this->emit('hide-upsert-role-modal');

        }
    }

    /**
     * Show deleting role confirmation modal
     * 
     * @param Role $role
     */
    public function showDeleteRoleModal(Role $role)
    {
        $this->roleId = $role->id;

        $this->name = $role->name;

        $this->emit('show-delete-role-modal');
        
    }

    /**
     * Soft deletes a role instance
     */
    public function deleteRole()
    {
        try {

            $role = Role::findOrFail($this->roleId);

            $this->authorize('delete', $role);
    
            if($role->delete()){

                $this->reset(['roleId', 'name']);

                session()->flash('status', "The role has, {$role->name}, been deleted");

                $this->emit('hide-delete-role-modal');
            }

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Failed to update the role";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();

            else $message = App::environment('local') ? $exception->getMessage() : $message;
            
            session()->flash('error', $message);

            $this->emit('hide-delete-role-modal');
        }
    }

    /**
     * Show modal for updating role permissions
     * 
     * @param Role $role - the role to update
     */
    public function showUpdatePermissionsModal(Role $role)
    {
        $this->roleId = $role->id;

        $this->name = $role->name;

        $this->selectedPermissions = array_fill_keys($role->permissions->pluck('id')->all(), 'true');

        $this->emit('show-update-permissions-modal');
    }

    /**
     * Update role permissins for the currntly elected role
     */
    public function updatePermissions()
    {
        $data = $this->validate([
            'selectedPermissions' => ['required', 'array']
        ]);

        $payload = array_filter($data['selectedPermissions'], fn($value, $key) => $value == 'true', ARRAY_FILTER_USE_BOTH);

        try {

            /** @var Role */
            $role = Role::findOrFail($this->roleId);

            $this->authorize('managePermissions', $role);

            $role->permissions()->sync(array_keys($payload));

            $this->reset(['roleId', 'name', 'selectedPermissions']);

            session()->flash('status', 'Role permissions updated');

            $this->emit('hide-update-permissions-modal');

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Failed to update the role";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();

            else $message = App::environment('local') ? $exception->getMessage() : $message;
            
            session()->flash('error', $message);

            $this->emit('hide-update-permissions-modal');
            
        }
        
    }

    /**
     * Restore a role that was trashed
     * 
     * @param mixed $roleId
     */
    public function restoreRole($roleId)
    {
        try {

            /** @var Role */
            $role = Role::where('id', $roleId)->withTrashed()->firstOrFail();

            $this->authorize('restore', $role);

            $role->restore();

            session()->flash('status', "The role, {$role->name}, has been successfully been restored");


        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Failed to restore the role";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();

            else $message = App::environment('local') ? $exception->getMessage() : $message;
            
            session()->flash('error', $message);
            
        }
    }

    /**
     * Completely delete a role from the database
     * 
     * @param mixed $roleId - the role to destroy
     */
    public function destroyRole($roleId)
    {
        
        try {

            /** @var Role */
            $role = Role::where('id', $roleId)->withTrashed()->firstOrFail();

            $this->authorize('forceDelete', $role);

            $role->forceDelete();

            session()->flash('status', "The role, {$role->name}, has been successfully been destroyed");


        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Failed to restore the role";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();

            else $message = App::environment('local') ? $exception->getMessage() : $message;
            
            session()->flash('error', $message);
            
        }
        
    }
}
