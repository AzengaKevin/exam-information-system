<?php

namespace App\Http\Livewire;

use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class AddStudentGuardians extends Component
{
    use WithPagination;

    public $student;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['showAddStudentGuardiansModal'];

    public $selectedGuardians = [];

    public function render()
    {
        return view('livewire.add-student-guardians', [
            'guardians' => $this->getPaginatedGuardians()
        ]);
    }

    public function getPaginatedGuardians()
    {
        return Guardian::with('auth')->paginate(12);
    }


    public function showAddStudentGuardiansModal(Student $student)
    {
        $this->student = $student;

        $this->emit('show-add-student-guardians-modal');
        
    }

    public function rules()
    {
        return [
            'selectedGuardians' => ['array', 'min:1']
        ];
    }

    public function addStudentGuardians()
    {
        $data = $this->validate();
        
        $guardianIds = array_filter($data["selectedGuardians"], function($value, $key){
            return boolval($value);
        }, ARRAY_FILTER_USE_BOTH);

        try {

            $this->student->guardians()->syncWithoutDetaching(array_keys($guardianIds));

            $this->reset(['selectedGuardians']);
            
            $this->emitTo('students', 'addStudentGuardiansFeedback', [
                'type' => 'status',
                'message' => $this->student->name . ' guardians successfully Added'
            ]);
            
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);
            
            $this->emitTo('students', 'addStudentGuardiansFeedback', [
                'type' => 'error',
                'message' => 'Student guardians addition failed'
            ]);
            
        }
        
    }
}
