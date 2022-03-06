<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessagesController extends Controller
{

    /**
     * Creates the MessageController instance
     * 
     * @return void
     */
    public function __construct() {

        $this->middleware(['auth']);

        $this->authorizeResource(Message::class);

    }

    /**
     * Show the current user own messages
     * 
     * @param Request $request
     */
    public function index(Request $request)
    {
        $trashed = boolval($request->trashed);

        return view('messages.index', compact('trashed'));
    }
}
