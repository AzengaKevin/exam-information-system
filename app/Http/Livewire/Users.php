<?php

namespace App\Http\Livewire;

use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $userId;

    public $name;
    public $email;
    public $active;

    public function render()
    {
        return view('livewire.users', [
            'users' => $this->getPaginatedUsers()
        ]);
    }

    public function getPaginatedUsers()
    {
        $id = Auth::id();

        return User::where('id', '<>', $id)->orderBy('name')->paginate(24);
    }

    /**
     * Show upsert user modal for editing and updating user
     * 
     * @param User $user
     */
    public function editUser(User $user)
    {
        
        $this->userId = $user->id;

        $this->name = $user->name;
        $this->email = $user->email;
        $this->active = $user->active;

        $this->emit('show-upsert-user-modal');
    }

    public function rules()
    {
        return [
            'name' => ['bail', 'required', 'string'],
            'email' => ['bail', 'required', 'email', Rule::unique('users')->ignore($this->userId)],
            'active' => ['bail', 'nullable']
        ];
    }

    public function updateUser()
    {
        $data = $this->validate();

        try {

            /** @var User */
            $user = User::findOrFail($this->userId);

            if($user->update($data)){

                session()->flash('status', 'Users successfully updated');

                $this->emit('hide-upsert-user-modal');
            }

            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'user-id' => $this->userId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

        }
    }
}
