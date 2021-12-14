<?php

namespace App\Http\Livewire;

use App\Models\Level;
use App\Models\Stream;
use App\Models\Student;
use Livewire\Component;
use App\Models\LevelUnit;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\Paginator;

class LevelUnitStudents extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $departmentId;

    public LevelUnit $levelUnit;

    public function mount(LevelUnit $levelUnit)
    {
        $this->levelUnit = $levelUnit;
    }
    
    public function render()
    {
        return view('livewire.level-unit-students', [
            'students' => $this->getPaginatedLevelUnitStudents(),
        ]);
    }

    /**
     * Get students from the respective level unit
     * 
     * @return Paginator
     */
    public function getPaginatedLevelUnitStudents()
    {
        return $this->levelUnit->students()->paginate(24);
    }
}
