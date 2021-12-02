<?php

namespace App\Http\Livewire;

use App\Models\Hostel;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class Hostels extends Component
{
    public $hostelId;

    public $name;
    public $slug;
    public $description;

    public function render()
    {
        return view('livewire.hostels', [
            'hostels' => $this->getPaginatedHostels()
        ]);
    }

    public function getPaginatedHostels()
    {
        return Hostel::get();
    }

    /**
     * Show upsert hostel modal for editing and updating hostel
     * 
     * @param Hostel $hostel
     */
    public function editHostel(Hostel $hostel)
    {
        
        $this->hostelId = $hostel->id;

        $this->name = $hostel->name;
        $this->description = $hostel->description;

        $this->emit('show-upsert-hostel-modal');
    }

    public function rules()
    {
        return [
            'name' => ['bail', 'required', 'string', Rule::unique('hostels')->ignore($this->hostelId)],
            'description' => ['bail', 'nullable']
        ];
    }

    function createHostel()
    {
        $this->validate();
        
        try {

            Hostel::create([
                'name'=>$this->name,
                'description'=>$this->description ,
            ]);
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'user-id' => $this->userId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

        }
        $this->emit('hide-upsert-hostel-modal');
    }


    public function updateHostel()
    {
        $data = $this->validate();

        try {

            /** @var User */
            $hostel = Hostel::findOrFail($this->hostelId);

            if($hostel->update($data)){

                session()->flash('status', 'hostel successfully updated');

                $this->emit('hide-upsert-hostel-modal');
            }
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'user-id' => $this->hostelId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

        }
    }

    public function showDeleteHostelModal(Hostel $hostel)
    {
        $this->departmtneId = $hostel->id;

        $this->name = $hostel->name;

        $this->emit('show-delete-hostel-modal');
        
    }

    public function deleteDepartment(Hostel $hostel)
    {
        try {

            $hostel = Hostel::findOrFail($this->departmtneId);

            if($hostel->delete()){

                $this->reset(['hostelId', 'name']);

                session()->flash('status', 'The hostel has been successfully deleted');

                $this->emit('hide-delete-hostel-modal');
            }

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'hostel-id' => $this->departmtneId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

            session()->flash('error', 'A fatal error has occurred');

            $this->emit('hide-delete-hostel-modal');
        }
    }
}
