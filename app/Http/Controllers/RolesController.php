<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);

        $this->authorizeResource(Role::class);
    }

    public function index(Request $request)
    {
        return view('roles.index');
    }
}
