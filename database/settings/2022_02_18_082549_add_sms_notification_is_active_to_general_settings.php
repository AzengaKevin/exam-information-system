<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class AddSmsNotificationIsActiveToGeneralSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.sms_notification_is_active', false);
    }
}
