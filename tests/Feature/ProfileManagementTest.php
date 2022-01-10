<?php

namespace Tests\Feature;

use App\Http\Livewire\UpdateUserPassword;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group profile */
    public function testAUserCanVisitOwnProfile()
    {
        $this->withoutExceptionHandling();

        Notification::fake();

        $user = $this->login();

        $response = $this->get(route('profile'));

        $response->assertOk();

        $response->assertViewIs('profile');

        $response->assertViewHasAll(['user']);

        $response->assertSeeLivewire('user-profile-photo');

        $response->assertSeeLivewire('user-profile-information');

        $response->assertSeeLivewire('update-user-password');
        
    }

    /** @group profile */
    public function testAuthenticateUserCanUpdateProfilePhoto()
    {
        $this->withoutExceptionHandling();

        $user = $this->login();

        Storage::fake('public');

        Notification::fake();

        $payload = [
            'file' => UploadedFile::fake()->create('test-image.jpg', 437.45, 'image/jpeg')
        ];

        Livewire::test(UserProfilePhoto::class, ['user' => $user])
            ->set('file', $payload['file'])
            ->call('updateUserProfilePhoto');

        $this->assertNotNull($user->fresh()->profilePhoto);

        $this->assertTrue(Storage::disk('public')->exists($user->fresh()->profilePhoto->path));
        
    }

    /** @group profile */
    public function testAnAuthenticatedUserCanUpdateOwnProfile()
    {
        $this->withoutExceptionHandling();

        Notification::fake();

        $user = $this->login();

        $payload = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
        ];

        Livewire::test(UserProfileInformation::class, ['user' => $user])
            ->set('name', $payload['name'])
            ->set('email', $payload['email'])
            ->set('phone', $payload['phone'])
            ->call('updateUserProfileInformation');
            
        $this->assertEquals($payload['name'], $user->fresh()->name);
        $this->assertEquals($payload['email'], $user->fresh()->email);
        $this->assertEquals(Str::start($payload['phone'], '254'), $user->fresh()->phone);
    }

    /** @group profile */
    public function testUserCanUpdateAccountPassword()
    {
        $this->withExceptionHandling();

        Notification::fake();

        /** @var User */
        $user = User::factory()->create(['password' => Hash::make($password = $this->faker->password())]);

        $payload = array(
            'current_password' => $password,
            'password' => $newPassword = $this->faker->password(),
            'password_confirmation' => $newPassword
        );

        Livewire::test(UpdateUserPassword::class, ['user' => $user])
            ->set('current_password', $payload['current_password'])
            ->set('password', $payload['password'])
            ->set('password_confirmation', $payload['password_confirmation'])
            ->call('updatePassword');
        
        $this->assertTrue(Auth::attempt([
            'email' => $user->email,
            'password' => $newPassword
        ]));
    }

    /** 
     * Creates a user log them in and return the user
     * 
     * @return User
     */
    private function login() : User
    {

        /** @var Authenticatable */
        $user = User::factory()->create();

        $this->actingAs($this->user = $user);

        return $user;
        
    }
}
