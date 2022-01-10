<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\Rules\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator;
use App\Actions\Fortify\UpdateUserPassword as FortifyUpdateUserPassword;

class UpdateUserPassword extends Component
{
    public $current_password;
    public $password;
    public $password_confirmation;

    public User $user;

    /**
     * Called when the component mounts
     * 
     * @param User $user
     */
    public function mount(User $user)
    {
        $this->user = $user;
    }

    public function render()
    {
        return view('livewire.update-user-password');
    }

    public function rules()
    {
        return [
            'current_password' => ['bail', 'required'],
            'password' => ['required', 'string', new Password, 'confirmed'],
            'password_confirmation' => []
        ];
    }

    /**
     * Update current user password in the database
     */
    public function updatePassword()
    {

        $data = $this->validate();

        $result = $this->withValidator(function(Validator $validator) use($data){

            $validator->after(function ($validator) use($data) {
                if (! isset($data['current_password']) || ! Hash::check($data['current_password'], $this->user->password)) {
                    $validator->errors()->add('current_password', __('The provided password does not match your current password.'));
                }
            });

        })->validate();

        try {

            $action = new FortifyUpdateUserPassword;

            $action->update($this->user, $result);

            $this->reset(['current_password', 'password', 'password_confirmation']);

            session()->flash('status', 'Password successfully updated');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', $exception->getMessage());

        }
    }
}
