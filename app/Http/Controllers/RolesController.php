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

    /**
     * Show a list of all roles
     * 
     * @param Request $request
     * 
     * @return View
     */
    public function index(Request $request)
    {
        return view('roles.index', [
            'trashed' => $request->trashed
        ]);
    }
}
