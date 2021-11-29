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
    public function index(Request $request)
    {
        return view('users.index');
        
    }
}
