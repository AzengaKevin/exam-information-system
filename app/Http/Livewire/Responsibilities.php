<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use App\Models\Responsibility;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class Responsibilities extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $responsibilityId;

    public $name;
    public $slug;
    public $description;

    public function render()
    {
        return view('livewire.responsibilities', [
            'responsibilities' => $this->getPaginatedResponsibilities()
        ]);
    }

    public function getPaginatedResponsibilities()
    {
        return Responsibility::paginate(24);
    }

    /**
     * Show upsert user modal for editing and updating user
     * 
     * @param User $user
     */
    public function editResponsibility(Responsibility $responsibility)
    {
        
        $this->responsibilityId = $responsibility->id;

        $this->name = $responsibility->name;
        $this->description = $responsibility->description;

        $this->emit('show-upsert-responsibility-modal');
    }

    public function rules()
    {
        return [
            'name' => ['bail', 'required', 'string', Rule::unique('responsibilities')->ignore($this->responsibilityId)],
            'description' => ['bail', 'nullable']
        ];
    }

    function createResponsibility()
    {
        $this->validate();
        
        try {

            Responsibility::create([
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
        $this->emit('hide-upsert-responsibility-modal');
    }


    public function updateResponsibility()
    {
        $data = $this->validate();

        try {

            /** @var User */
            $responsibility = Responsibility::findOrFail($this->responsibilityId);

            if($responsibility->update($data)){

                session()->flash('status', 'responsibility successfully updated');

                $this->emit('hide-upsert-responsibility-modal');
            }
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'user-id' => $this->responsibilityId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

        }
    }

    public function showDeleteResponsibilityModal(Responsibility $responsibility)
    {
        $this->departmentId = $responsibility->id;

        $this->name = $responsibility->name;

        $this->emit('show-delete-responsibility-modal');
        
    }

    public function deleteResponsibility(Responsibility $responsibility)
    {
        try {

            $responsibility = Responsibility::findOrFail($this->departmentId);

            if($responsibility->delete()){

                $this->reset(['responsibilityId', 'name']);

                session()->flash('status', 'The responsibility has been successfully deleted');

                $this->emit('hide-delete-responsibility-modal');
            }

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'responsibility-id' => $this->departmentId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

            session()->flash('error', 'A fatal error has occurred');

            $this->emit('hide-delete-responsibility-modal');
        }
    }
}
