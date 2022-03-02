<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public ?string $school_website;
    public string $school_address;
    public string $school_telephone_number;
    public string $school_email_address;
    public int $current_academic_year;
    public string $current_term;
    public ?string $logo;
    public ?int $school_manager_responsibility_id;
    public ?int $exam_manager_responsibility_id;
    public bool $sms_notification_is_active;
    public string $report_form_disclaimer;

    public static function group() : string
    {
        return 'general';
    }

}
