<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
    /**
     * Show a list of messages that belongs to the current user
     * 
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        // Authorize the request

        return view('user.messages.index');
        
    }
}
