<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{

    public function __construct() {

        $this->middleware('auth');

        $this->authorizeResource(User::class);
        
    }

    /**
     * Show a list of user
     * 
     * @return View
     */
    public function index(Request $request)
    {
        $trashed = $request->trashed;

        $roleId = $request->role_id;

        if(boolval($trashed)) $this->authorize('viewTrashed', User::class);

        return view('users.index', compact('trashed', 'roleId'));
        
    }
}
