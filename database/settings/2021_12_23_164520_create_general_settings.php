<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateGeneralSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('general', function(SettingsBlueprint $blueprint) : void{
            $blueprint->add('school_website', route('welcome'));
            $blueprint->add('school_address', '1973 Kisumu');
            $blueprint->add('school_telephone_number', '+254-57-2020164');
            $blueprint->add('school_email_address', 'kisumuboys1973@gmail.com');
            $blueprint->add('current_academic_year', intval(now()->format('Y')));
            $blueprint->add('current_term', 'Term 1');
        });
    }
}
