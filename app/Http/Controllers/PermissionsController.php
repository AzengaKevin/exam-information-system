<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);

        $this->authorizeResource(Permission::class);
    }

    /**
     * Show a list of all permissions
     * 
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $trashed = $request->trashed;

        if (boolval($trashed)) $this->authorize('viewTrashed', Permission::class);

        return view('permissions.index', compact('trashed'));
    }
}
