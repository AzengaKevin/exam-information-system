<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;

class UserProfileInformation extends Component
{

    public $name;
    public $email;
    public $phone;

    public $userId;

    public $user;

    public function mount(User $user)
    {
        /** @var User */
        $user = Auth::user();

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->phone = $user->phone;
        $this->email = $user->email;

        $this->user = $user;
    }

    public function render()
    {
        return view('livewire.user-profile-information');
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->userId)
            ],

            'phone' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($this->userId)
            ]
        ];
    }

    /**
     * Update current user database record
     */
    public function updateUserProfileInformation()
    {
        $data = $this->validate();

        $updateProfileInfo = new UpdateUserProfileInformation;

        $updateProfileInfo->update($this->user, $data);

        session()->flash('status', 'Profile information successfully updated');

        $this->emit('hide-update-user-profile-information-modal');
        
    }
}
