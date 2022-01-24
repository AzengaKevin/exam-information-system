<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateGeneralSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('general', function(SettingsBlueprint $blueprint) : void{
            $blueprint->add('school_website', route('welcome'));
            $blueprint->add('school_address', '54712 Nairobi');
            $blueprint->add('school_telephone_number', '+254711220033');
            $blueprint->add('school_email_address', 'toomuch573@gmail.com');
            $blueprint->add('current_academic_year', intval(now()->format('Y')));
            $blueprint->add('current_term', 'Term 1');
            $blueprint->add('logo', null);
            $blueprint->add('school_manager_responsibility_id', 1);
            $blueprint->add('exam_manager_responsibility_id', 4);
        });
    }
}
