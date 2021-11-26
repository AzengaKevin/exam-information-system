<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Permission;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class Permissions extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $permissionId;

    public $name;
    public $slug;
    public $description;

    public function render()
    {
        return view('livewire.permissions', [
            'permissions' => $this->getPaginatedPermission()
        ]);
    }

    public function getPaginatedPermission()
    {
        return Permission::orderBy('name')->paginate(24);
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
        $this->description = $permission->description;

        $this->emit('show-upsert-permission-modal');
    }

    public function rules()
    {
        return [
            'name' => ['bail', 'required', 'string'],
            'description' => ['bail', 'nullable']
        ];
    }

    function createPermission()
    {
        $this->validate();
        
        try {

            Permission::create([
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
        $this->emit('hide-upsert-permission-modal');
    }


    public function updatePermission()
    {
        $data = $this->validate();

        try {

            /** @var User */
            $permission = Permission::findOrFail($this->permissionId);

            if($permission->update($data)){

                session()->flash('status', 'permission successfully updated');

                $this->emit('hide-upsert-permission-modal');
            }
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'user-id' => $this->userId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

        }
    }

    public function showDeletePermissionModal(Permission $permission)
    {
        $this->permissionId = $permission->id;

        $this->name = $permission->name;

        $this->emit('show-delete-permission-modal');
        
    }

    public function deletePermission(Permission $permission)
    {
        try {

            $permission = Permission::findOrFail($this->permissionId);

            if($permission->delete()){

                $this->reset(['permissionId', 'name']);

                session()->flash('status', 'The permission has been successfully deleted');

                $this->emit('hide-delete-permission-modal');
            }

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'permission-id' => $this->permissionId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

            session()->flash('error', 'A fatal error has occurred');

            $this->emit('hide-delete-permission-modal');
        }
    }
}
