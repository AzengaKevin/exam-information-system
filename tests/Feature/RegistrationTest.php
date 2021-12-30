<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class RegistrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group registration */
    public function testAGuestCanVisitTheRegistrationPage()
    {
        $this->withoutExceptionHandling();

        $this->expectException(RouteNotFoundException::class);

        $response = $this->get(route('register'));

        $response->assertOk();

        $response->assertViewIs('auth.register');

    }

    /** @group registration */
    public function testOneWithCorrectCredentialsCanSuccessfullyRegistered()
    {
        $this->withoutExceptionHandling();

        $this->expectException(RouteNotFoundException::class);

        Mail::fake();

        $userData = $this->getData();

        $response = $this->post(route('register'), $userData);

        $this->assertAuthenticated();

        $response->assertRedirect(route('dashboard'));
        
    }

    /** @group registration */
    public function testRegistrationRequiredFieds()
    {

        $this->expectException(RouteNotFoundException::class);

        $requiredFields = ['name', 'email', 'phone', 'password'];

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
            'phone' => "707427854",
            'password' => 'elephant69',
            'password_confirmation' => 'elephant69'
        ];
    }
}
