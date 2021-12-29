<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MessagesController extends Controller
{
    /**
     * Show the current user own messages
     * 
     * @param Request $request
     */
    public function index(Request $request)
    {
        return view('messages.index');
    }
}
