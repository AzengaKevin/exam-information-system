<?php

namespace Tests\Feature;

use App\Models\Exam;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
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

        $response = $this->get(route('settings.index'));

        $response->assertOk();

        $response->assertViewIs('settings.index');

        $response->assertViewHasAll(['systemSettings', 'generalSettings']);
        
    }

    /** @group settings */
    public function testAuthorizedUsersCanUpdateSettings()
    {
        $this->withoutExceptionHandling();

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
            ]
        ]);

        $response->assertRedirect();
        
    }
}
