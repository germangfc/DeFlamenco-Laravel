<?php

namespace Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;


class LoginControllerApiTest extends TestCase
{
    use RefreshDatabase;

    private string $url = '/api/login';

    public function test_login_successfully()
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'tipo' => 'admin',
        ]);

        $response = $this->postJson($this->url, [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

    public function test_login_campos_requeridos()
    {
        $response = $this->postJson($this->url, []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_not_found()
    {
        $response = $this->postJson($this->url, [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(404)
            ->assertJson(['error' => 'User not Found']);
    }

    public function test_login_not_an_admin(){
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'tipo' => 'cliente',
        ]);

        $response = $this->postJson($this->url, [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(401)
            ->assertJson(['error' => 'Unauthorized, you are not an admin']);
    }

    public function test_login_incorrect_password(){
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'tipo' => 'admin',
        ]);

        $response = $this->postJson($this->url, [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson(['error' => 'Unauthorized, wrong credentials']);
    }

    public function test_login_credenciales_incorrectos()
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'tipo' => 'admin',
        ]);

        $this->actingAs($user, 'api');

        $response = $this->postJson($this->url, [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

}
