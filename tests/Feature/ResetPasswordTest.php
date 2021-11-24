<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group reset-password */
    public function testAGuestCanVisitRequestResetPasswordView()
    {
        $this->withoutExceptionHandling();

        $response = $this->get(route('password.request'));

        $response->assertOk();

        $response->assertViewIs('auth.forgot-password');
        
    }

    /** @group reset-password */
    public function testAnEmailCanBeSentSuccessFully()
    {
        /** @var User */
        $user = User::factory()->create(['email' => 'test@test.com']);

        Mail::fake();

        $response = $this->post(route('password.email'), [
            'email' => $user->email
        ]);

        $response->assertSessionHasNoErrors();
    }

    /** @group reset-password */
    public function testEmailIsRequiredToResetPassword()
    {
        Mail::fake();

        $response = $this->post(route('password.email'));

        $response->assertStatus(302);

        $response->assertSessionHasErrors(['email']);
    }

    /** @group reset-password */
    public function testDontSentPasswordForAStrangeEmail()
    {
        Mail::fake();

        $response = $this->post(route('password.email'), [
            'email' => 'test@test.com'
        ]);
        
        Mail::assertNothingSent();

        $response->assertRedirect();

        $response->assertSessionHasErrors();
    }

    /** @group reset-password */
    public function testResetPasswordLinkCanVisited()
    {
        Mail::fake();

        /** @var User */
        $user = User::factory()->create(['email' => 'test@test.com']);

        $this->post(route('password.email'), ['email' => $user->email]);

        $payload = DB::table('password_resets')
            ->first(['email', 'token']);
        
        $this->assertNotNull($payload);

        $response = $this->get(route('password.reset', [
            'token' => $payload->token,
            'email' => $payload->email,
        ]));

        $response->assertSessionHasNoErrors();

    }

    /** @group password */
    public function _testPasswordGetsUpdatedWhenTheUserResets()
    {
        Mail::fake();

        /** @var User */
        $user = User::factory()->create(['email' => 'test@test.com']);

        $this->post(route('password.email'), ['email' => $user->email]);

        $payload = DB::table('password_resets')
            ->first(['email', 'token']);
        
        $this->assertNotNull($payload);

        $response = $this->post(route('password.update'), [
            'email' => $payload->email,
            'token' => $payload->token,
            'password' => $password = 'elephant69',
            'password_confirmation' => $password
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertTrue(Auth::attempt([
            'email' => $payload->email,
            'password' => $password
        ]));
        
    }
}
