<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Guardian;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use App\Rules\MustBeKenyanPhone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class Guardians extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $guardianId;
    public $userId;

    public $name;
    public $email;
    public $phone;
    public $profession;
    public $location;

    public function render()
    {
        return view('livewire.guardians', [
            'guardians' => $this->getPaginatedGuardians()
        ]);
    }

    public function getPaginatedGuardians()
    {
        return Guardian::latest()->paginate(24);
    }

    /**
     * Hook method for updating the phone and making sure it starts with 254
     */
    public function updatedPhone($value)
    {
        $this->phone = Str::start($value, '254');
        
    }

    public function rules()
    {
        return [
            'name' => ['bail', 'required', 'string'],
            'email' => ['bail', 'required', 'string', 'email', Rule::unique('users')->ignore($this->userId)],
            'phone' => ['bail', 'required', Rule::unique('users')->ignore($this->userId), new MustBeKenyanPhone()],
            'profession' => ['bail', 'nullable'],
            'location' => ['bail', 'nullable']
        ];
    }

    public function addGuardian()
    {
        $data = $this->validate();

        try {

            DB::beginTransaction();

            /** @var Guardian */
            $guardian = Guardian::create($data);

            if($guardian){

                $user = $guardian->auth()->create(array_merge($data, [
                    'password' => Hash::make('password')
                ]));

                if($user){

                    DB::commit();

                    $this->reset(['name', 'email', 'profession', 'location']);

                    $this->resetPage();

                    session()->flash('status', 'A Guardian has successfully been added');

                    $this->emit('hide-upsert-guardian-modal');
                }

            }
            
        } catch (\Exception $exception) {
            
            DB::rollBack();

            Log::error($exception->getMessage(), [
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

            session()->flash('error', 'A fatal error occurred check the logs');

            $this->emit('hide-upsert-guardian-modal');
        }
        
    }

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

    public function updateGuardian()
    {
        $data = $this->validate();

        try {

            DB::beginTransaction();

            /** @var Guardian */
            $guardian = Guardian::findOrFail($this->guardianId);

            if($guardian->update($data)){
                
                if($guardian->auth->update($data)){

                    DB::commit();

                    $this->reset(['guardianId', 'userId', 'name', 'email', 'profession', 'location']);

                    session()->flash('status', 'A guardian has successfully been updated');

                    $this->emit('hide-upsert-guardian-modal');

                }
            }

        } catch (\Exception $exception) {
            
            DB::rollBack();

            Log::error($exception->getMessage(), [
                'guardian-id' => $this->guardianId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

            session()->flash('error', 'A fatal error occurred check the logs');

            $this->emit('hide-upsert-guardian-modal');
        }
    }


    public function showDeleteGuardianModal(Guardian $guardian)
    {
        $this->guardianId = $guardian->id;

        $this->name = optional($guardian->auth)->name ?? 'Anonymous';

        $this->emit('show-delete-guardian-modal');
    }

    public function deleteGuardian()
    {
        
        try {
            
            /** @var Guardian */
            $guardian = Guardian::findOrFail($this->guardianId);

            DB::beginTransaction();

            if($guardian->auth) $guardian->auth->delete();

            if($guardian->delete()){

                DB::commit();

                $this->reset(['name', 'guardianId']);

                $this->resetPage();

                session()->flash('status', 'Guardian successfully deleted');

                $this->emit('hide-delete-guardian-modal');
            }
            
        } catch (\Exception $exception) {
            
            DB::rollBack();

            Log::error($exception->getMessage(), [
                'guardian-id' => $this->guardianId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

            session()->flash('error', 'A fatal error occurred check the logs');

            $this->emit('hide-delete-guardian-modal');
        }
        
    }
}