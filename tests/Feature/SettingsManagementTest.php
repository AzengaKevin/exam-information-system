<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\Permission;
use App\Models\Responsibility;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Settings\GeneralSettings;
use App\Settings\SystemSettings;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SettingsManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private Role $role;

    public function setUp(): void
    {
        parent::setUp();

        $this->role = Role::factory()->create();

        /** @var Authenticatable */
        $user = User::factory()->create(['role_id' => $this->role->id]);

        $this->actingAs($user);
    }

    /** @group settings */
    public function testAuthorizedUserCanVisitSettingsPage()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Settings View']));

        $response = $this->get(route('settings.index'));

        $response->assertOk();

        $response->assertViewIs('settings.index');

        $response->assertViewHasAll(['systemSettings', 'generalSettings', 'responsibilities']);
        
    }

    /** @group settings */
    public function testAuthorizedUsersCanUpdateSettings()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Settings Update']));

        Responsibility::factory(2)->create();

        $response = $this->patch(route('settings.update'), [
            'system' => [
                'school_name' => $schoolName = $this->faker->sentence(),
                'school_type' => $schoolType = 'girls',
                'school_level' => $schoolLevel = 'primary',
                'school_has_streams' => $schoolHasStreams = false
            ],
            'general' => [
                'school_website' => $schoolWebsite = $this->faker->url(),
                'school_address' => $schoolAddress = $this->faker->address(),
                'school_telephone_number' => $schoolTelephone = $this->faker->e164PhoneNumber(),
                'school_email_address' => $schoolEmailAddress = $this->faker->safeEmail(),
                'current_academic_year' => $currentAcademicYear = $this->faker->year(),
                'current_term' => $currentTerm = $this->faker->randomElement(Exam::termOptions()),
                'school_manager_responsibility_id' => Responsibility::first()->id,
                'exam_manager_responsibility_id' => Responsibility::latest()->first()->id,
                'sms_notification_is_active' => $smsNotificatioActive = $this->faker->boolean(),
                'report_form_disclaimer' => $reportFormDisclaimer = $this->faker->sentence(),
            ]
        ]);

        /** @var SystemSettings */
        $systemSettings = app(SystemSettings::class);

        /** @var GeneralSettings */
        $generalSettings = app(GeneralSettings::class);

        $this->assertEquals($schoolName, $systemSettings->school_name);
        $this->assertEquals($schoolType, $systemSettings->school_type);
        $this->assertEquals($schoolLevel, $systemSettings->school_level);
        $this->assertEquals($schoolHasStreams, $systemSettings->school_has_streams);

        $this->assertEquals($schoolWebsite, $generalSettings->school_website);
        $this->assertEquals($schoolAddress, $generalSettings->school_address);
        $this->assertEquals($schoolTelephone, $generalSettings->school_telephone_number);
        $this->assertEquals($schoolEmailAddress, $generalSettings->school_email_address);
        $this->assertEquals($currentAcademicYear, $generalSettings->current_academic_year);
        $this->assertEquals($currentTerm, $generalSettings->current_term);
        $this->assertEquals($smsNotificatioActive, $generalSettings->sms_notification_is_active);
        $this->assertEquals(Responsibility::first()->id, $generalSettings->school_manager_responsibility_id);
        $this->assertEquals(Responsibility::latest()->first()->id, $generalSettings->exam_manager_responsibility_id);
        $this->assertEquals($reportFormDisclaimer, $generalSettings->report_form_disclaimer);

        $response->assertRedirect();
        
    }
}
