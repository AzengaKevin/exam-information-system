<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SystemSettings extends Settings
{
    public string $school_name;
    public string $school_type;
    public string $school_level;
    public bool $school_has_streams;
    
    public static function group() : string
    {
        return 'system';
    }
}
