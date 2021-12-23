<?php

namespace App\Http\Controllers;

use App\Settings\SystemSettings;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, SystemSettings $systemSettings)
    {
        return view('welcome', [
            'settings' => $systemSettings
        ]);
    }
}
