<?php

namespace App\Http\Livewire;

use App\Models\Guardian;
use Livewire\Component;
use Livewire\WithPagination;

class Guardians extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $guardianId;
    public $userId;

    public $name;
    public $email;
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
}
