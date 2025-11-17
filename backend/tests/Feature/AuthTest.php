<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Patient;
use App\Models\Medecin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'user' => [
                    'id',
                    'email',
                    'role',
                ],
            ]);
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Identifiants invalides',
            ]);
    }

    /** @test */
    public function patient_can_register()
    {
        $response = $this->postJson('/api/patients/register', [
            'email' => 'patient@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '+21612345678',
            'date_naissance' => '1990-01-01',
            'sexe' => 'M',
            'cin' => '12345678',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => [
                    'id',
                    'email',
                    'role',
                ],
                'token',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'patient@example.com',
            'role' => 'patient',
        ]);

        $this->assertDatabaseHas('patients', [
            'cin' => '12345678',
        ]);
    }

    /** @test */
    public function medecin_can_register()
    {
        $response = $this->postJson('/api/medecins/register', [
            'email' => 'medecin@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'first_name' => 'Dr. Jane',
            'last_name' => 'Smith',
            'phone' => '+21698765432',
            'numero_ordre' => '1234/2020',
            'specialite' => 'Médecine générale',
            'adresse_cabinet' => '123 Rue de la Santé',
            'ville' => 'Tunis',
            'governorate' => 'Tunis',
            'tarif_consultation' => 50.00,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => [
                    'id',
                    'email',
                    'role',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'medecin@example.com',
            'role' => 'medecin',
        ]);

        $this->assertDatabaseHas('medecins', [
            'numero_ordre' => '1234/2020',
            'validation_status' => 'pending',
        ]);
    }

    /** @test */
    public function authenticated_user_can_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Déconnexion réussie',
            ]);

        $this->assertCount(0, $user->tokens);
    }

    /** @test */
    public function authenticated_user_can_get_their_profile()
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'first_name' => 'John',
                'last_name' => 'Doe',
            ]);
    }

    /** @test */
    public function registration_requires_valid_email()
    {
        $response = $this->postJson('/api/patients/register', [
            'email' => 'invalid-email',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '+21612345678',
            'date_naissance' => '1990-01-01',
            'sexe' => 'M',
            'cin' => '12345678',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function registration_requires_password_confirmation()
    {
        $response = $this->postJson('/api/patients/register', [
            'email' => 'patient@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'DifferentPassword!',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '+21612345678',
            'date_naissance' => '1990-01-01',
            'sexe' => 'M',
            'cin' => '12345678',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }
}
