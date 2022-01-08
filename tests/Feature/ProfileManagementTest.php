<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use App\Http\Livewire\UserProfilePhoto;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use App\Http\Livewire\UserProfileInformation;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    
    public function setUp() : void
    {
        parent::setUp();

        /** @var Authenticatable */
        $user = User::factory()->create();

        $this->actingAs($this->user = $user);
    }

    /** @group profile */
    public function testAUserCanVisitOwnProfile()
    {
        $this->withoutExceptionHandling();

        $response = $this->get(route('profile'));

        $response->assertOk();

        $response->assertViewIs('profile');

        $response->assertViewHasAll(['user']);
        
    }

    /** @group profile */
    public function testAuthenticateUserCanUpdateProfilePhoto()
    {
        $this->withoutExceptionHandling();

        Storage::fake('public');

        $payload = [
            'file' => UploadedFile::fake()->create('test-image.jpg', 437.45, 'image/jpeg')
        ];

        Livewire::test(UserProfilePhoto::class, ['user' => $this->user])
            ->set('file', $payload['file'])
            ->call('updateUserProfilePhoto');

        $this->assertNotNull($this->user->fresh()->profilePhoto);

        $this->assertTrue(Storage::disk('public')->exists($this->user->fresh()->profilePhoto->path));
        
    }

    /** @group profile */
    public function testAnAuthenticatedUserCanUpdateOwnProfile()
    {
        $this->withoutExceptionHandling();

        Notification::fake();

        $payload = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
        ];

        Livewire::test(UserProfileInformation::class, ['user' => $this->user])
            ->set('name', $payload['name'])
            ->set('email', $payload['email'])
            ->set('phone', $payload['phone'])
            ->call('updateUserProfileInformation');
            
        $this->assertEquals($payload['name'], $this->user->fresh()->name);
        $this->assertEquals($payload['email'], $this->user->fresh()->email);
        $this->assertEquals(Str::start($payload['phone'], '254'), $this->user->fresh()->phone);
    }
}
