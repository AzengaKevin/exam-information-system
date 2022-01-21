<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\Guardian;
use App\Notifications\SendPasswordNotification;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use App\Rules\MustBeKenyanPhone;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class Guardians extends Component
{
    use WithPagination, AuthorizesRequests;

    protected $paginationTheme = 'bootstrap';

    public $trashed = false;

    public $guardianId;
    public $userId;

    public $name;
    public $email;
    public $phone;
    public $profession;
    public $location;

    /**
     * Lifecycle method that only executes once when the component is mounting
     * 
     * @param string $trashed
     */
    public function mount(string $trashed = null)
    {
        $this->trashed = boolval($trashed);
    }

    /**
     * Lifecycle method to render the component when it's state changes
     * 
     * @return View
     */
    public function render()
    {
        return view('livewire.guardians', [
            'guardians' => $this->getPaginatedGuardians()
        ]);
    }

    /**
     * Get paginated guardians from the  database
     * 
     * @return Paginator
     */
    public function getPaginatedGuardians()
    {
        $guardiansQuery = Guardian::query();

        if($this->trashed) $guardiansQuery->onlyTrashed();

        return $guardiansQuery->paginate(24);
    }

    /**
     * Hook method for updating the phone and making sure it starts with 254
     * 
     * @param mixed $value
     */
    public function updatedPhone($value)
    {
        $this->phone = Str::start($value, '254');
    }

    /**
     * Component fields validation rules
     * 
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['bail', 'required', 'string'],
            'email' => ['bail', 'nullable', 'string', 'email', Rule::unique('users')->ignore($this->userId)],
            'phone' => ['bail', 'required', Rule::unique('users')->ignore($this->userId), new MustBeKenyanPhone()],
            'profession' => ['bail', 'nullable'],
            'location' => ['bail', 'nullable']
        ];
    }

    /**
     * Persist a new guardian to the database
     */
    public function addGuardian()
    {
        $data = $this->validate();

        try {

            $this->authorize('create', Guardian::class);
            $this->authorize('create', User::class);

            DB::transaction(function() use($data){
    
                /** @var Guardian */
                $guardian = Guardian::create($data);

                /** @var User */
                $user = $guardian->auth()->create(array_merge($data, [
                    'password' => Hash::make($password = Str::random(6))
                ]));

                // Sending email verification link to the user
                if(!empty($user->email)) $user->sendEmailVerificationNotification();

                // Send the guardian a password
                $user->notifyNow(new SendPasswordNotification($password));

            });

            $this->reset(['name', 'email', 'profession', 'location']);

            $this->resetPage();

            $this->resetValidation();

            session()->flash('status', "A guardian, has successfully been added");

            $this->emit('hide-upsert-guardian-modal');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            $message = "Woops! Adding guardian operation failed";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();
            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);

            $this->emit('hide-upsert-guardian-modal');
        }
        
    }

    /**
     * Show a modal for editing the specified guardian
     * 
     * @param Guardian $guardian
     */
    public function editGuardian(Guardian $guardian)
    {
        
        $this->guardianId = $guardian->id;
        $this->userId = $guardian->auth->id;

        $this->name = $guardian->auth->name;
        $this->email = $guardian->auth->email;
        $this->phone = $guardian->auth->phone;

        $this->profession = $guardian->profession;
        $this->location = $guardian->location;

        $this->emit('show-upsert-guardian-modal');

    }

    /**
     * Updating a guardian database record
     */
    public function updateGuardian()
    {
        $data = $this->validate();

        try {

            /** @var Guardian */
            $guardian = Guardian::findOrFail($this->guardianId);

            $this->authorize('update', $guardian);

            $this->authorize('update', $guardian->auth);

            DB::transaction(function() use($guardian, $data){
                $guardian->update($data);
                $guardian->auth->update($data);
            });

            $this->reset(['guardianId', 'userId', 'name', 'email', 'profession', 'location']);

            session()->flash('status', "The guardian, {$guardian->auth->name}, has been updated, successfully");

            $this->emit('hide-upsert-guardian-modal');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            $message = "Woops! Updating guardian operation failed";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();
            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);

            $this->emit('hide-upsert-guardian-modal');
        }
    }

    /**
     * Show a deletion confirmation modal for the specified guardian
     * 
     * @param Guardian $quardian
     */
    public function showDeleteGuardianModal(Guardian $guardian)
    {
        $this->guardianId = $guardian->id;

        $this->name = optional($guardian->auth)->name ?? 'Anonymous';

        $this->emit('show-delete-guardian-modal');
    }

    /**
     * Trash a guardian
     */
    public function deleteGuardian()
    {
        
        try {
            
            /** @var Guardian */
            $guardian = Guardian::findOrFail($this->guardianId);

            $this->authorize('delete', $guardian);

            DB::transaction(function() use($guardian){

                // if($guardian->auth) $guardian->auth->delete();

                $guardian->delete();

            });

            $this->reset(['name', 'guardianId']);

            $this->resetPage();

            session()->flash('status', "The guardian, {$guardian->auth->name} , successfully deleted");

            $this->emit('hide-delete-guardian-modal');
            
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            $message = "Woops! Deleting guardian operation failed";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();
            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);

            $this->emit('hide-delete-guardian-modal');
        }
        
    }

    /**
     * Restores a soft deleted guardian
     * 
     * @param mixed $guardianId
     */
    public function restoreGuardian($guardianId)
    {
        try {

            /** @var Guardian */
            $guardian = Guardian::where('id', $guardianId)->withTrashed()->firstOrFail();

            $this->authorize('restore', $guardian);
            
            $guardian->restore();

            session()->flash('status', "Guardian has been restores");

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            $message = "Woops! Restoring guardian operation failed";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();
            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);
            
        }
    }

    /**
     * Deleting guardian from the system
     * 
     * @param mixed $guardianId
     */
    public function destroyGuardian($guardianId)
    {
        
        try {

            /** @var Guardian */
            $guardian = Guardian::where('id', $guardianId)->withTrashed()->firstOrFail();

            $this->authorize('forceDelete', $guardian);

            $this->authorize('forceDelete', $guardian->auth);

            DB::transaction(function() use($guardian){

                $guardian->forceDelete();

                $guardian->auth->forceDelete();

            });

            session()->flash('status', "Guardian completely deleted from the system");
            
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            $message = "Woops! Deleting guardian from application operation failed";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();
            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);
            
        }
        
    }
}