<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /** 
     * Show the current user profile
     * 
     * @param Request $request
     */
    public function show(Request $request)
    {
        $user = $request->user();

        return view('profile', compact('user'));
    }
}
