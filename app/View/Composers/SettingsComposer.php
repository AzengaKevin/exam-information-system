<?php

namespace App\View\Composers;

use App\Settings\GeneralSettings;
use App\Settings\SystemSettings;
use Illuminate\View\View;

class SettingsComposer
{
    public SystemSettings $systemSettings;
    public GeneralSettings $generalSettings;

    public function __construct(SystemSettings $systemSettings, GeneralSettings $generalSettings) {

        $this->systemSettings = $systemSettings;

        $this->generalSettings = $generalSettings;
    }

    public function compose(View $view)
    {
        
        $view->with([
            'systemSettings' => $this->systemSettings,
            'generalSettings' => $this->generalSettings,
        ]);
        
    }
}
