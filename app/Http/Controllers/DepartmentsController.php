<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);

        $this->authorizeResource(Department::class);
    }

    /**
     * Show a list of all department
     * 
     * @param Request $request
     */
    public function index(Request $request)
    {
        $trashed = $request->trashed;

        if(boolval($trashed)) $this->authorize('viewTrashed', Department::class);

        return view('departments.index', compact('trashed'));
    }
}
