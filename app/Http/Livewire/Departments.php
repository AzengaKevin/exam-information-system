<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Department;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class Departments extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $departmentId;

    public $name;
    public $slug;
    public $description;

    public function render()
    {
        return view('livewire.departments', [
            'departments' => $this->getPaginatedDepartments()
        ]);
    }

    public function getPaginatedDepartments()
    {
        return Department::paginate(24);
    }

    /**
     * Show upsert user modal for editing and updating user
     * 
     * @param User $user
     */
    public function editDepartment(Department $department)
    {
        
        $this->departmentId = $department->id;

        $this->name = $department->name;
        $this->description = $department->description;

        $this->emit('show-upsert-department-modal');
    }

    public function rules()
    {
        return [
            'name' => ['bail', 'required', 'string', Rule::unique('departments')->ignore($this->departmentId)],
            'description' => ['bail', 'nullable']
        ];
    }

    function createDepartment()
    {
        $this->validate();
        
        try {

            Department::create([
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
        $this->emit('hide-upsert-department-modal');
    }


    public function updateDepartment()
    {
        $data = $this->validate();

        try {

            /** @var User */
            $department = Department::findOrFail($this->departmentId);

            if($department->update($data)){

                session()->flash('status', 'department successfully updated');

                $this->emit('hide-upsert-department-modal');
            }
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'user-id' => $this->departmentId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

        }
    }

    public function showDeleteDepartmentModal(Department $department)
    {
        $this->departmtneId = $department->id;

        $this->name = $department->name;

        $this->emit('show-delete-department-modal');
        
    }

    public function deleteDepartment(Department $department)
    {
        try {

            $department = Department::findOrFail($this->departmtneId);

            if($department->delete()){

                $this->reset(['departmentId', 'name']);

                session()->flash('status', 'The department has been successfully deleted');

                $this->emit('hide-delete-department-modal');
            }

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'department-id' => $this->departmtneId,
                'action' => __CLASS__ . '@' . __METHOD__
            ]);

            session()->flash('error', 'A fatal error has occurred');

            $this->emit('hide-delete-department-modal');
        }
    }
}
