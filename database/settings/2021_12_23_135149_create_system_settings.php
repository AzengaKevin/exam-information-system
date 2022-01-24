<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateSystemSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('system', function(SettingsBlueprint $blueprint) : void{
            $blueprint->add('school_name', 'Diskus School');
            $blueprint->add('school_type', 'mixed');
            $blueprint->add('school_level', 'secondary');
            $blueprint->add('school_has_streams', true);
            $blueprint->add('boarding_school', true);
        });

    }
}
