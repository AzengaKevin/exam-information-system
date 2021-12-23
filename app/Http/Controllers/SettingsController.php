<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Settings\SystemSettings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    
    public function index(Request $request, SystemSettings $systemSettings)
    {
        return view('settings.index', compact('systemSettings'));
    }

    public function update(UpdateSettingsRequest $updateSettingsRequest, SystemSettings $systemSettings)
    {
        $data = $updateSettingsRequest->validated();
        
        $data = array_filter($data, fn($value, $key) => !is_null($value), ARRAY_FILTER_USE_BOTH);

        foreach ($data as $key => $value) {
            $systemSettings->$key = $value;
        }

        session()->flash('status', 'Settings has been successfully updated');

        $systemSettings->save();

        return redirect()->back();
        
    }
}
