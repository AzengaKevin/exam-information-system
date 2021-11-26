<?php

namespace App\Http\Livewire;

use App\Models\Student;
use Livewire\Component;
use Livewire\WithPagination;

class Students extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        return view('livewire.students',[
            'students' => $this->getPaginatedStudents()
        ]);
    }

    public function getPaginatedStudents()
    {
        return Student::orderBy('adm_no')->paginate(24);
    }
}
