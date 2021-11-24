<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        return view('livewire.users', [
            'users' => $this->getPaginatedUsers()
        ]);
    }

    public function getPaginatedUsers()
    {
        $id = Auth::id();

        return User::where('id', '<>', $id)->orderBy('name')->paginate(24);
    }
}
