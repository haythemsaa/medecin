<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Patient;
use App\Models\Medecin;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    private User $patientUser;
    private User $medecinUser;
    private Patient $patient;
    private Medecin $medecin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create patient user
        $this->patientUser = User::factory()->create(['role' => 'patient']);
        $this->patient = Patient::factory()->create(['user_id' => $this->patientUser->id]);

        // Create medecin user
        $this->medecinUser = User::factory()->create(['role' => 'medecin']);
        $this->medecin = Medecin::factory()->create([
            'user_id' => $this->medecinUser->id,
            'validation_status' => 'validated',
            'tarif_consultation' => 50.00,
        ]);
    }

    /** @test */
    public function patient_can_create_appointment()
    {
        $appointmentDate = Carbon::tomorrow()->setTime(10, 0);

        $response = $this->actingAs($this->patientUser)
            ->postJson('/api/appointments', [
                'medecin_id' => $this->medecin->id,
                'date' => $appointmentDate->format('Y-m-d'),
                'time' => '10:00',
                'type' => 'video',
                'motif' => 'Consultation de routine',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'appointment' => [
                    'id',
                    'date',
                    'time',
                    'status',
                    'type',
                    'montant',
                ],
            ]);

        $this->assertDatabaseHas('appointments', [
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
            'status' => 'pending',
            'montant' => 50.00,
        ]);
    }

    /** @test */
    public function patient_can_view_their_appointments()
    {
        Appointment::factory()->count(3)->create([
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
        ]);

        $response = $this->actingAs($this->patientUser)
            ->getJson('/api/appointments');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'appointments');
    }

    /** @test */
    public function medecin_can_view_their_appointments()
    {
        Appointment::factory()->count(5)->create([
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
        ]);

        $response = $this->actingAs($this->medecinUser)
            ->getJson('/api/appointments');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'appointments');
    }

    /** @test */
    public function patient_can_cancel_their_appointment()
    {
        $appointment = Appointment::factory()->create([
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
            'date' => Carbon::tomorrow(),
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($this->patientUser)
            ->postJson("/api/appointments/{$appointment->id}/cancel", [
                'cancellation_reason' => 'Changement de programme',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Rendez-vous annulé avec succès',
            ]);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'cancelled',
        ]);
    }

    /** @test */
    public function cannot_cancel_appointment_within_24_hours()
    {
        $appointment = Appointment::factory()->create([
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
            'date' => Carbon::now()->addHours(12),
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($this->patientUser)
            ->postJson("/api/appointments/{$appointment->id}/cancel", [
                'cancellation_reason' => 'Changement de programme',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'error' => 'appointment_not_cancellable',
            ]);
    }

    /** @test */
    public function patient_cannot_view_other_patients_appointments()
    {
        $otherPatientUser = User::factory()->create(['role' => 'patient']);
        $otherPatient = Patient::factory()->create(['user_id' => $otherPatientUser->id]);

        $appointment = Appointment::factory()->create([
            'patient_id' => $otherPatient->id,
            'medecin_id' => $this->medecin->id,
        ]);

        $response = $this->actingAs($this->patientUser)
            ->getJson("/api/appointments/{$appointment->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function appointment_requires_future_date()
    {
        $response = $this->actingAs($this->patientUser)
            ->postJson('/api/appointments', [
                'medecin_id' => $this->medecin->id,
                'date' => Carbon::yesterday()->format('Y-m-d'),
                'time' => '10:00',
                'type' => 'video',
                'motif' => 'Consultation',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['date']);
    }

    /** @test */
    public function appointment_requires_valid_type()
    {
        $response = $this->actingAs($this->patientUser)
            ->postJson('/api/appointments', [
                'medecin_id' => $this->medecin->id,
                'date' => Carbon::tomorrow()->format('Y-m-d'),
                'time' => '10:00',
                'type' => 'invalid-type',
                'motif' => 'Consultation',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }

    /** @test */
    public function unauthenticated_user_cannot_create_appointment()
    {
        $response = $this->postJson('/api/appointments', [
            'medecin_id' => $this->medecin->id,
            'date' => Carbon::tomorrow()->format('Y-m-d'),
            'time' => '10:00',
            'type' => 'video',
        ]);

        $response->assertStatus(401);
    }
}
