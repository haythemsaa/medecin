<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UrgentConsultationTest extends TestCase
{
    use RefreshDatabase;

    protected User $patientUser;
    protected Patient $patient;
    protected User $medecinUser;
    protected Medecin $medecin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create patient
        $this->patientUser = User::factory()->create(['role' => 'patient']);
        $this->patient = Patient::factory()->create(['user_id' => $this->patientUser->id]);

        // Create medecin
        $this->medecinUser = User::factory()->create(['role' => 'medecin']);
        $this->medecin = Medecin::factory()->create([
            'user_id' => $this->medecinUser->id,
            'status' => 'validated',
            'accepts_urgent' => true,
            'urgent_surcharge_percentage' => 50.00,
            'urgent_response_time' => 60,
            'tarif' => 100.00,
        ]);
    }

    public function test_can_get_available_doctors_for_urgent_consultation()
    {
        $response = $this->actingAs($this->patientUser)
            ->getJson('/api/urgent-consultations/available');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'doctors' => [
                    '*' => [
                        'id',
                        'user',
                        'specialite',
                        'tarif',
                        'urgent_price',
                        'urgent_response_time',
                        'available_in_minutes',
                        'rating',
                    ]
                ],
                'total',
            ]);
    }

    public function test_can_filter_available_doctors_by_specialty()
    {
        $response = $this->actingAs($this->patientUser)
            ->getJson('/api/urgent-consultations/available?specialty=' . $this->medecin->specialite);

        $response->assertStatus(200);

        $doctors = $response->json('doctors');
        foreach ($doctors as $doctor) {
            $this->assertEquals($this->medecin->specialite, $doctor['specialite']);
        }
    }

    public function test_can_request_urgent_consultation()
    {
        $response = $this->actingAs($this->patientUser)
            ->postJson('/api/urgent-consultations/request', [
                'medecin_id' => $this->medecin->id,
                'motif' => 'Forte fièvre et douleurs',
                'symptoms_description' => 'Symptômes depuis ce matin, température 39°C',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'appointment' => [
                    'id',
                    'is_urgent',
                    'urgent_fee',
                    'urgent_requested_at',
                ]
            ]);

        $this->assertDatabaseHas('appointments', [
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
            'is_urgent' => true,
            'status' => 'pending',
        ]);
    }

    public function test_urgent_fee_is_calculated_correctly()
    {
        $response = $this->actingAs($this->patientUser)
            ->postJson('/api/urgent-consultations/request', [
                'medecin_id' => $this->medecin->id,
                'motif' => 'Test urgent consultation',
            ]);

        $appointment = $response->json('appointment');
        $expectedFee = $this->medecin->tarif * 1.5; // 100 * 1.5 = 150

        $this->assertEquals($expectedFee, $appointment['urgent_fee']);
    }

    public function test_doctor_can_accept_urgent_consultation()
    {
        $appointment = Appointment::factory()->create([
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
            'is_urgent' => true,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->medecinUser)
            ->postJson("/api/urgent-consultations/{$appointment->id}/accept");

        $response->assertStatus(200);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_cannot_request_urgent_from_doctor_who_does_not_accept_urgent()
    {
        $this->medecin->update(['accepts_urgent' => false]);

        $response = $this->actingAs($this->patientUser)
            ->postJson('/api/urgent-consultations/request', [
                'medecin_id' => $this->medecin->id,
                'motif' => 'Test',
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Ce médecin n\'accepte pas les consultations urgentes',
            ]);
    }

    public function test_doctor_can_toggle_urgent_availability()
    {
        $response = $this->actingAs($this->medecinUser)
            ->postJson('/api/urgent-consultations/toggle-availability', [
                'accepts_urgent' => false,
                'urgent_surcharge_percentage' => 75.00,
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('medecins', [
            'id' => $this->medecin->id,
            'accepts_urgent' => false,
            'urgent_surcharge_percentage' => 75.00,
        ]);
    }

    public function test_can_get_urgent_consultation_stats()
    {
        // Create some urgent appointments
        Appointment::factory()->count(3)->create([
            'medecin_id' => $this->medecin->id,
            'is_urgent' => true,
            'status' => 'completed',
            'urgent_fee' => 150.00,
        ]);

        Appointment::factory()->count(2)->create([
            'medecin_id' => $this->medecin->id,
            'is_urgent' => true,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->medecinUser)
            ->getJson('/api/urgent-consultations/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_urgent',
                'completed',
                'pending',
                'cancelled',
                'total_revenue',
                'average_response_time',
            ]);

        $stats = $response->json();
        $this->assertEquals(5, $stats['total_urgent']);
        $this->assertEquals(3, $stats['completed']);
        $this->assertEquals(2, $stats['pending']);
        $this->assertEquals(450.00, $stats['total_revenue']); // 3 * 150
    }
}
