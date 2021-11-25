<?php

namespace App\Http\Livewire;

use App\Models\Teacher;
use Livewire\Component;
use Livewire\WithPagination;

class Teachers extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        return view('livewire.teachers', [
            'teachers' => $this->getPaginatedTeachers()
        ]);
    }

    public function getPaginatedTeachers()
    {
        return Teacher::latest()->paginate(24);
    }
}
