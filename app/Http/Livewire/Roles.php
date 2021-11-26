<?php

namespace App\Http\Livewire;

use App\Models\Permission;
use App\Models\Role;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class Roles extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $roleId;

    public $name;
    public $slug;
    public $description;

    public $selectedPermissions = [];

    public function render()
    {
        return view('livewire.roles', [
            'roles' => $this->getPaginatedRoles(),
            'permissions' => $this->getAllPermissions()
        ]);
    }

    public function getPaginatedRoles()
    {
        return Role::paginate(24);
    }

    public function getAllPermissions()
    {
        return Permission::all(['id', 'name']);
    }

    /**
     * Show upsert user modal for editing and updating user
     * 
     * @param User $user
     */
    public function editRole(Role $role)
    {
        
        $this->roleId = $role->id;

        $this->name = $role->name;
        $this->description = $role->description;

        $this->emit('show-upsert-role-modal');
    }

    public function rules()
    {
        return [
            'name' => ['bail', 'required', 'string'],
            'description' => ['bail', 'nullable']
        ];
    }

    function createRole()
    {
        $this->validate();
        
        try {

            Role::create([
                'name'=>$this->name,
                'description'=>$this->description ,
                'slug'=>Str::slug($this->name)
            ]);
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'user-id' => $this->userId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

        }
        $this->emit('hide-upsert-role-modal');
    }


    public function updateRole()
    {
        $data = $this->validate();

        try {

            /** @var User */
            $role = Role::findOrFail($this->roleId);

            if($role->update($data)){

                session()->flash('status', 'role successfully updated');

                $this->emit('hide-upsert-role-modal');
            }
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'user-id' => $this->userId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

        }
    }

    public function showDeleteRoleModal(Role $role)
    {
        $this->roleId = $role->id;

        $this->name = $role->name;

        $this->emit('show-delete-role-modal');
        
    }

    public function deleteRole(Role $role)
    {
        try {

            $role = Role::findOrFail($this->roleId);

            if($role->delete()){

                $this->reset(['roleId', 'name']);

                session()->flash('status', 'The role has been successfully deleted');

                $this->emit('hide-delete-role-modal');
            }

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'role-id' => $this->roleId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

            session()->flash('error', 'A fatal error has occurred');

            $this->emit('hide-delete-role-modal');
        }
    }

    public function showUpdatePermissionsModal(Role $role)
    {
        $this->roleId = $role->id;

        $this->name = $role->name;

        foreach ($role->permissions->pluck('id')->toArray() as $permission) {
            $this->selectedPermissions[$permission] = 'true';
        }

        $this->emit('show-update-permissions-modal');
    }

    public function updatePermissions()
    {
        $data = $this->validate([
            'selectedPermissions' => ['required', 'array']
        ]);

        $payload = array_filter($data['selectedPermissions'], function($value, $key){
            return $value == 'true';
        }, ARRAY_FILTER_USE_BOTH);

        try {

            /** @var Role */
            $role = Role::findOrFail($this->roleId);

            $result = $role->permissions()->sync(array_keys($payload));

            $this->reset(['roleId', 'name', 'selectedPermissions']);

            session()->flash('status', 'Role permissions updated');

            $this->emit('hide-update-permissions-modal');

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'role-id' => $this->roleId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

            session()->flash('error', 'A database error occurred');

            $this->emit('hide-update-permissions-modal');
            
        }
        
    }
}
