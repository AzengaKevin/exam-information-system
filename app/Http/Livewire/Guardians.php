<?php

namespace App\Http\Livewire;

use App\Imports\GuardiansImport;
use App\Models\User;
use App\Models\Level;
use Livewire\Component;
use App\Models\Guardian;
use App\Models\LevelUnit;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use App\Rules\MustBeKenyanPhone;
use App\Services\StudentService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Notifications\SendPasswordNotification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Guardians extends Component
{
    use WithPagination, WithFileUploads, AuthorizesRequests;

    protected $paginationTheme = 'bootstrap';

    public $trashed = false;

    public $guardianId;
    public $userId;

    public $name;
    public $email;
    public $phone;
    public $profession;
    public $location;

    // Import guardians properties
    public $guardiansImportFile;
    public $level_id;
    public $level_unit_id;

    public $levels;
    public $levelUnits;


    /**
     * Lifecycle method that only executes once when the component is mounting
     * 
     * @param string $trashed
     */
    public function mount(string $trashed = null)
    {
        $this->trashed = boolval($trashed);

        $this->levels = $this->getAllLevels();

        $this->levelUnits = $this->getAllLevelUnits();
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
     * Get all database levels
     * 
     * @return Collection
     */
    public function getAllLevels()
    {
        return Level::all(['id', 'name']);
    }

    /**
     * Get all datbase streams
     */
    public function getAllLevelUnits()
    {
        return LevelUnit::all(['id', 'alias']);
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
     * Download an excel file with format to upload student scores
     */
    public function downloadStudentsGuardiansFile(StudentService $studentService)
    {
        $data = $this->validate([
            'level_id' => ['nullable', 'integer'],
            'level_unit_id' => ['nullable', 'integer']
        ]);

        /** @var Collection */
        $studentsWithGuardians = $studentService->getStudentsWithPrimaryGuardians($data);

        $studentsWithGuardians = $studentsWithGuardians->filter(fn($item) => is_null($item->guardian_name) && is_null($item->guardian_phone));

        // Adding the title column to the students with gurdians collection
        $studentsWithGuardians->prepend(
            (object)array(
                "student_id" => "STUDENT ID",
                "student_name" => "STUDENT NAME",
                "guardian_name" => "GUARDIAN NAME",
                "guardian_phone" => "GUARDIAN PHONE",
                "guardian_email" => "GUARDIAN EMAIL",
                "location" => "GUARDIAN LOCATION",
                "profession" => "GUARDIAN PROFESSION"
            )
        );

        $timeStr = now()->format("Y_m_d_His");

        return $studentsWithGuardians->downloadExcel("{$timeStr}_students_guardians.xlsx");
    }

    /**
     * Import the students guardians from the excel file
     */
    public function importStudentsGuardians()
    {
        $data = $this->validate(['guardiansImportFile' => ['file', 'mimes:xlsx,csv,ods,xlsm,xltx,xltm,xls,xlt,xml']]);

        /** @var UploadedFile */
        $guardiansFile = $data['guardiansImportFile'];

        try {
            
            Excel::import(new GuardiansImport, $guardiansFile);
    
            session()->flash('status', 'Students guardian successfully imported');
            
            $this->emit('hide-import-guardians-spreadsheet-modal');

        } catch (\Exception $exception) {
         
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Woops! An error occurred while trying to import guardians";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();
            
            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);
            
            $this->emit('hide-import-guardians-spreadsheet-modal');
            
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