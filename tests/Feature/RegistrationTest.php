<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group registration */
    public function testAGuestCanVisitTheRegistrationPage()
    {
        $this->withoutExceptionHandling();

        $response = $this->get(route('register'));

        $response->assertOk();

        $response->assertViewIs('auth.register');

    }

    /** @group registration */
    public function testOneWithCorrectCredentialsCanSuccessfullyRegistered()
    {
        $this->withoutExceptionHandling();

        Mail::fake();

        $userData = $this->getData();

        $response = $this->post(route('register'), $userData);

        $this->assertAuthenticated();

        $response->assertRedirect(route('home'));
        
    }

    /** @group registration */
    public function testRegistrationRequiredFieds()
    {
        $requiredFields = ['name', 'email', 'password'];

        foreach ($requiredFields as $field) {

            $response = $this->post(route('register'), array_merge(
                $this->getData(),
                [$field => null]
            ));

            $response->assertSessionHasErrors([$field]);

        }
    }

    private function getData(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'password' => 'elephant69',
            'password_confirmation' => 'elephant69'
        ];
    }
}
