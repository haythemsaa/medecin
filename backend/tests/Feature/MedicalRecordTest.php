<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Patient;
use App\Models\Medecin;
use App\Models\MedicalRecord;
use App\Models\MedicalConsent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalRecordTest extends TestCase
{
    use RefreshDatabase;

    private User $patientUser;
    private User $medecinUser;
    private Patient $patient;
    private Medecin $medecin;
    private MedicalRecord $medicalRecord;

    protected function setUp(): void
    {
        parent::setUp();

        // Create patient
        $this->patientUser = User::factory()->create(['role' => 'patient']);
        $this->patient = Patient::factory()->create(['user_id' => $this->patientUser->id]);
        $this->medicalRecord = MedicalRecord::factory()->create(['patient_id' => $this->patient->id]);

        // Create medecin
        $this->medecinUser = User::factory()->create(['role' => 'medecin']);
        $this->medecin = Medecin::factory()->create(['user_id' => $this->medecinUser->id]);
    }

    /** @test */
    public function patient_can_view_their_medical_record()
    {
        $response = $this->actingAs($this->patientUser)
            ->getJson('/api/medical-records/my-record');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'medical_record' => [
                    'id',
                    'blood_type',
                    'allergies',
                    'chronic_diseases',
                    'current_treatments',
                ],
            ]);
    }

    /** @test */
    public function patient_can_update_their_medical_record()
    {
        $response = $this->actingAs($this->patientUser)
            ->putJson('/api/medical-records/my-record', [
                'blood_type' => 'A+',
                'allergies' => ['Pénicilline', 'Arachides'],
                'chronic_diseases' => ['Diabète'],
                'current_treatments' => ['Metformine 500mg'],
                'height' => 175,
                'weight' => 70,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Dossier médical mis à jour avec succès',
            ]);

        $this->assertDatabaseHas('medical_records', [
            'patient_id' => $this->patient->id,
            'blood_type' => 'A+',
            'height' => 175,
            'weight' => 70,
        ]);
    }

    /** @test */
    public function medecin_can_access_medical_record_with_consent()
    {
        // Create active consent
        MedicalConsent::factory()->create([
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->medecinUser)
            ->getJson("/api/medical-records/patients/{$this->patient->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'medical_record' => [
                    'id',
                    'blood_type',
                    'allergies',
                ],
            ]);
    }

    /** @test */
    public function medecin_cannot_access_medical_record_without_consent()
    {
        $response = $this->actingAs($this->medecinUser)
            ->getJson("/api/medical-records/patients/{$this->patient->id}");

        $response->assertStatus(403)
            ->assertJson([
                'error' => 'medical_record_access_denied',
            ]);
    }

    /** @test */
    public function patient_can_create_consent()
    {
        $response = $this->actingAs($this->patientUser)
            ->postJson('/api/medical-records/consents', [
                'medecin_id' => $this->medecin->id,
                'duration_months' => 12,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Consentement créé avec succès',
            ]);

        $this->assertDatabaseHas('medical_consents', [
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
            'status' => 'active',
        ]);
    }

    /** @test */
    public function patient_can_revoke_consent()
    {
        $consent = MedicalConsent::factory()->create([
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->patientUser)
            ->deleteJson("/api/medical-records/consents/{$consent->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Consentement révoqué avec succès',
            ]);

        $this->assertDatabaseHas('medical_consents', [
            'id' => $consent->id,
            'status' => 'revoked',
        ]);
    }

    /** @test */
    public function patient_can_view_access_history()
    {
        $response = $this->actingAs($this->patientUser)
            ->getJson('/api/medical-records/access-history');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_logs',
            ]);
    }

    /** @test */
    public function cannot_access_other_patients_medical_record()
    {
        $otherPatientUser = User::factory()->create(['role' => 'patient']);
        $otherPatient = Patient::factory()->create(['user_id' => $otherPatientUser->id]);

        $response = $this->actingAs($this->patientUser)
            ->getJson("/api/medical-records/patients/{$otherPatient->id}");

        $response->assertStatus(403);
    }
}
