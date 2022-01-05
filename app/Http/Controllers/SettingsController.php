<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Settings\GeneralSettings;
use App\Settings\SystemSettings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    
    /**
     * Show a list of system and general settings
     * 
     * @param Request $request
     * @param SystemSettings $systemSettings
     * @param GeneralSettings $generalSettings
     */
    public function index(Request $request, SystemSettings $systemSettings, GeneralSettings $generalSettings)
    {
        return view('settings.index', compact('systemSettings', 'generalSettings'));
    }

    /**
     * Update user and general settings based on user settings
     * 
     * @param UpdateSettingsRequest $request
     * @param SystemSettings $systemSettings
     * @param GeneralSettings $generalSettings
     */
    public function update(UpdateSettingsRequest $updateSettingsRequest, SystemSettings $systemSettings, GeneralSettings $generalSettings)
    {
        $data = $updateSettingsRequest->validated();

        foreach ($data['system'] as $key => $value) {

            if (in_array($key, ['school_has_streams', 'boarding_school'])) {

                $systemSettings->$key = boolval($value);

            }else{

                $systemSettings->$key = $value;

            }

        }
        
        if (!array_key_exists('school_has_streams', $data['system'])) {
            $systemSettings->school_has_streams = false;
        }
        
        if (!array_key_exists('boarding_school', $data['system'])) {
            $systemSettings->boarding_school = false;
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
