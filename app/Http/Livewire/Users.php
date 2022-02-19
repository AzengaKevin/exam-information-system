<?php

namespace App\Http\Livewire;

use App\Models\Role;
use App\Models\User;
use App\Services\RoleService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination, AuthorizesRequests;

    protected $paginationTheme = 'bootstrap';

    public $roles;

    public $userId;

    public $name;
    public $email;
    public $active;
    public $role_id;

    public $trashed = false;
    public $currentRoleId;

    public $selectedUsers = [];

    public User $currentUser;

    /**
     * Lifecycle method that excute onces when the component is mounting
     * 
     * @param mixed $trashed
     */
    public function mount(RoleService $roleService, ?string $trashed = null, ?int $roleId = null)
    {
        $this->trashed = boolval($trashed);

        $this->currentUser = Auth::user();

        $this->currentRoleId = $roleId;

        $this->roles = $this->currentUser->isSuperAdmin()
            ? $roleService->getAllRoles([], true)
            : $roleService->getAllRoles();
    }

    /**
     * Lifecycle method that renders the component everytime it's state changes
     * 
     * @return View
     */
    public function render()
    {
        return view('livewire.users', [
            'users' => $this->getPaginatedUsers(),
        ]);
    }

    /**
     * Get all paginated users from the database
     * 
     * @return Paginator
     */
    public function getPaginatedUsers()
    {
        $usersQuery = User::orderBy('name');

        if(!$this->currentUser->isSuperAdmin()) $usersQuery->visible();

        if($this->trashed) $usersQuery->onlyTrashed();

        if($this->currentRoleId) $usersQuery->role($this->currentRoleId);

        return $usersQuery->paginate(24)->withQueryString();
    }

    /**
     * Show update user modal for editing and updating user
     * 
     * @param User $user
     */
    public function editUser(User $user)
    {
        
        $this->userId = $user->id;

        $this->name = $user->name;
        $this->email = $user->email;
        $this->active = $user->active;

        $this->role_id = $user->role_id;

        $this->emit('show-update-user-modal');
    }

    /**
     * User properties validation field
     * 
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['bail', 'required', 'string'],
            'email' => ['bail', 'nullable', 'email', Rule::unique('users')->ignore($this->userId)],
            'active' => ['bail', 'nullable'],
            'role_id' => ['nullable', 'integer']
        ];
    }

    /**
     * Updates a database user record
     */
    public function updateUser()
    {
        $data = $this->validate();

        try {

            /** @var User */
            $user = User::findOrFail($this->userId);

            $this->authorize('update', $user);

            if($user->update($data)){

                $this->reset(['userId', 'name', 'active', 'role_id']);

                session()->flash('status', 'Users successfully updated');

                $this->emit('hide-update-user-modal');
            }
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Updating user operation failed";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();
            else $message = App::environment('local') ? $exception->getMessage() : $message;
    
            session()->flash('error', $message);

            $this->emit('hide-update-user-modal');

        }
    }

    /**
     * Shows modal for deleting a user
     * 
     * @param User $user
     */
    public function showDeleteUserModal(User $user)
    {
        $this->userId = $user->id;

        $this->name = $user->name;

        $this->emit('show-delete-user-modal');
        
    }

    /**
     * Trashes a user
     */
    public function deleteUser()
    {
        try {
            
            /** @var User */
            $user = User::findOrFail($this->userId);

            $this->authorize('delete', $user);

            DB::transaction(function() use($user){

                // if($user->authenticatable) $user->authenticatable->delete();

                $user->delete();
    
                $this->reset(['userId', 'name']);

                $this->resetPage();

                $this->resetValidation();

                session()->flash('status', "{$user->name} has been trashed");

                $this->emit('hide-delete-user-modal');

            });

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'user-id' => $this->userId,
                'action' => __METHOD__
            ]);

            $message = "User deletion failed";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();
            else App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $exception);

            $this->emit('hide-delete-user-modal');

        }
    }

    /**
     * Toggles the active status of th specified user
     * 
     * @param User $user
     */
    public function toggleUserActiveStatus(User $user)
    {
        try {

            $this->authorize('update', $user);
            
            $data = array('active' => !boolval($user->active));
    
            if($user->update($data)){
    
                session()->flash('User successfully ' . $user->fresh()->active ? 'Activated' : 'Deactivated');
    
            }

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'user-id' => $this->userId,
                'action' => __METHOD__
            ]);

            $message = "User deletion failed";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();
            else App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $exception);
            
        }
        

    }

    /**
     * Updated multiple users roles at one go
     */
    public function bulkUsersRoleUpdate()
    {
        $data = $this->validate([
            'role_id' => ['bail', 'required', 'integer'],
            'selectedUsers' => ['bail', 'array', 'min:1']
        ]);

        $selectedUsers = array_filter($data['selectedUsers'], fn($value, $key) => boolval($value), ARRAY_FILTER_USE_BOTH);

        try {

            $this->authorize('bulkUpdate', User::class);

            DB::table('users')
                ->whereIn('id', array_keys($selectedUsers))
                ->update(['role_id' => $data['role_id']]);

                $this->reset(['role_id', 'selectedUsers']);

                session()->flash('status', 'Users roles updated');

                $this->emit('hide-users-bulk-role-update-modal');

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "User deletion failed";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();
            else App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $exception);

            $this->emit('hide-users-bulk-role-update-modal');
            
        }
        
    }

    /**
     * Restore trashed users
     * 
     * @param mixed $userId
     */
    public function restoreUser($userId)
    {
        try {
            
            /** @var User */
            $user = User::where('id', $userId)->withTrashed()->firstOrFail();

            $this->authorize('restore', $user);

            if($user->restore()){

                session()->flash('status', "{$user->name} has been restored");
            }

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "User restoration failed";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();
            else App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $exception);
            
        }
    }

    /**
     * Delete User From the database
     * 
     * @param mixed $userId
     */
    public function destroyUser($userId)
    {
        try {
            
            /** @var User */
            $user = User::where('id', $userId)->withTrashed()->firstOrFail();

            $this->authorize('forceDelete', $user);

            if($user->forceDelete()){

                session()->flash('status', "{$user->name} has been destroyed");
            }

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "User deletion failed";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();
            else App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $exception);
            
        }
    }
}
