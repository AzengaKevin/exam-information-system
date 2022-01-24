<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Settings\SystemSettings;
use App\Settings\GeneralSettings;
use Illuminate\Http\UploadedFile;
use App\Http\Requests\UpdateSettingsRequest;
use App\Models\File;
use App\Models\Responsibility;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    
    /**
     * Show a list of system and general settings where applicable
     * 
     * @param Request $request
     * @param SystemSettings $systemSettings
     * @param GeneralSettings $generalSettings
     * 
     */
    public function index(Request $request, SystemSettings $systemSettings, GeneralSettings $generalSettings)
    {
        $this->authorize('view-settings');
        
        $user = $request->user();

        $responsibilities = Responsibility::all(['id', 'name']);

        return view('settings.index', compact('systemSettings', 'generalSettings', 'user', 'responsibilities'));
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

        try {

            // Raw Files Handling
            if (isset($data['raw']['logo'])) {
    
                /** @var UploadedFile */
                $logoFile = $data['raw']['logo'];
    
                $path = $logoFile->store("images/application", 'public');
    
                /** @var File */
                $file = File::create([
                    'path' => $path,
                    'name' => $logoFile->getClientOriginalName(),
                    'extension' => $logoFile->extension(),
                    'type' => $logoFile->getMimeType()
                ]);

                $generalSettings->logo = $file->url();
                
            }            
    
            if(array_key_exists('system', $data)){
        
                // Updating System Settings
                foreach ($data['system'] as $key => $value) {
        
                    if (in_array($key, ['school_has_streams', 'boarding_school'])) {
        
                        $systemSettings->$key = boolval($value);
        
                    }else{
        
                        $systemSettings->$key = $value;
        
                    }
        
                }
                
                // Boolean System Keys handling if they're been unchecked
        
                if (!array_key_exists('school_has_streams', $data['system'])) {
                    $systemSettings->school_has_streams = false;
                }
                
                if (!array_key_exists('boarding_school', $data['system'])) {
                    $systemSettings->boarding_school = false;
                }
        
                $systemSettings->save();
            }
    
            // Updateing general settings
            foreach ($data['general'] as $key => $value) {
    
                $generalSettings->$key = $value;
            }
            
            $generalSettings->save();
            
            session()->flash('status', 'Settings has been successfully updated');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', 'An error occurred check with the administrator');
            
        }

        return redirect()->back();
        
    }
}
