<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class UserProfilePhoto extends Component
{
    use WithFileUploads;

    public User $user;

    public $file;

    /**
     * Lifecycle method called when the compnent is mounting
     * 
     * @param User $user
     */
    public function mount(User $user)
    {
        $this->user = $user;
    }

    public function render()
    {
        return view('livewire.user-profile-photo');
    }

    public function rules()
    {
        return ['file' => ['required', 'image', 'max:512']];
    }

    /**
     * Update user profile image implementation
     */
    public function updateUserProfilePhoto()
    {
        $data = $this->validate();

        try {

            $usernameSlug = Str::slug($this->user->name);

            /** @var UploadedFile */
            $file = $data['file'];
    
            $extension = $file->extension();
            $mimeType = $file->getMimeType();
                
            // Get rid of the previous record and file if it exists
            if($this->user->profilePhoto){
                if($this->user->profilePhoto->deleteFromStorage()){
                    $this->user->profilePhoto()->delete();
                }
            }
            
            $path = $file->storeAs("images/profiles", "$usernameSlug.$extension" , 'public');
    
            $this->user->profilePhoto()->create([
                'path' => $path,
                'extension' => $extension,
                'type' => $mimeType,
                'name' => "{$this->user->name}"
            ]);

            $this->emit('hide-update-user-profile-photo-modal');
            
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), [
                'user-id' => $this->user->id,
                'action' => __METHOD__
            ]);

            $this->emit('hide-update-user-profile-photo-modal');
            
        }



        
    }
    
}