<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Settings\GeneralSettings;
use App\Settings\SystemSettings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    
    public function index(Request $request, SystemSettings $systemSettings, GeneralSettings $generalSettings)
    {
        return view('settings.index', compact('systemSettings', 'generalSettings'));
    }

    public function update(UpdateSettingsRequest $updateSettingsRequest, SystemSettings $systemSettings, GeneralSettings $generalSettings)
    {
        $data = $updateSettingsRequest->validated();
        
        foreach ($data['system'] as $key => $value) {

            if ($key == 'school_has_streams') {

                $systemSettings->$key = boolval($value);

            }else{

                $systemSettings->$key = $value;

            }

        }
        
        if (!array_key_exists('school_has_streams', $data['system'])) {
            $systemSettings->school_has_streams = false;
        }

        $systemSettings->save();

        
        foreach ($data['general'] as $key => $value) {

            $generalSettings->$key = $value;
        }
        
        $generalSettings->save();
        
        session()->flash('status', 'Settings has been successfully updated');


        return redirect()->back();
        
    }
}
