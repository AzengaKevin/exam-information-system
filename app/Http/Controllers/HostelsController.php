<?php

namespace App\Http\Controllers;

use App\Models\Hostel;
use Illuminate\Http\Request;

class HostelsController extends Controller
{
    public function __construct()
    {
        return $this->middleware(['auth']);
    }

    public function index()
    {
        return view('hostels.index');
    }

    public function show(Hostel $hostel)
    {
        return view('hostels.show',compact('hostel'));
    }
}
