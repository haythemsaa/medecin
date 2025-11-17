<?php

namespace Tests\Feature;

use App\Models\Consultation;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionRenewal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrescriptionRenewalTest extends TestCase
{
    use RefreshDatabase;

    protected User $patientUser;
    protected Patient $patient;
    protected User $medecinUser;
    protected Medecin $medecin;
    protected Prescription $prescription;

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
        ]);

        // Create consultation
        $consultation = Consultation::factory()->create([
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
        ]);

        // Create renewable prescription
        $this->prescription = Prescription::factory()->create([
            'consultation_id' => $consultation->id,
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
            'is_renewable' => true,
            'renewals_allowed' => 3,
            'renewals_used' => 0,
            'valid_until' => Carbon::now()->addMonths(6),
            'medicaments' => ['Paracétamol 500mg', 'Ibuprofène 400mg'],
        ]);
    }

    public function test_can_get_renewable_prescriptions()
    {
        $response = $this->actingAs($this->patientUser)
            ->getJson('/api/prescription-renewals/renewable');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'prescriptions' => [
                    '*' => [
                        'id',
                        'medicaments',
                        'renewals_allowed',
                        'renewals_used',
                        'renewals_remaining',
                        'valid_until',
                        'medecin',
                    ]
                ]
            ]);

        $prescriptions = $response->json('prescriptions');
        $this->assertCount(1, $prescriptions);
        $this->assertEquals(3, $prescriptions[0]['renewals_remaining']);
    }

    public function test_can_request_prescription_renewal()
    {
        $response = $this->actingAs($this->patientUser)
            ->postJson('/api/prescription-renewals/request', [
                'prescription_id' => $this->prescription->id,
                'patient_notes' => 'Le traitement fonctionne bien, je voudrais continuer',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'renewal' => [
                    'id',
                    'prescription_id',
                    'patient_id',
                    'medecin_id',
                    'status',
                    'patient_notes',
                ]
            ]);

        $this->assertDatabaseHas('prescription_renewals', [
            'prescription_id' => $this->prescription->id,
            'patient_id' => $this->patient->id,
            'status' => 'pending',
        ]);
    }

    public function test_cannot_request_renewal_if_no_renewals_left()
    {
        $this->prescription->update([
            'renewals_used' => 3,
        ]);

        $response = $this->actingAs($this->patientUser)
            ->postJson('/api/prescription-renewals/request', [
                'prescription_id' => $this->prescription->id,
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Nombre maximum de renouvellements atteint',
            ]);
    }

    public function test_cannot_request_renewal_if_expired()
    {
        $this->prescription->update([
            'valid_until' => Carbon::now()->subDays(1),
        ]);

        $response = $this->actingAs($this->patientUser)
            ->postJson('/api/prescription-renewals/request', [
                'prescription_id' => $this->prescription->id,
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Cette ordonnance a expiré',
            ]);
    }

    public function test_cannot_request_renewal_if_already_pending()
    {
        PrescriptionRenewal::factory()->create([
            'prescription_id' => $this->prescription->id,
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->patientUser)
            ->postJson('/api/prescription-renewals/request', [
                'prescription_id' => $this->prescription->id,
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Une demande de renouvellement est déjà en cours',
            ]);
    }

    public function test_doctor_can_get_pending_renewals()
    {
        PrescriptionRenewal::factory()->count(3)->create([
            'medecin_id' => $this->medecin->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->medecinUser)
            ->getJson('/api/prescription-renewals/pending');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'renewals' => [
                    '*' => [
                        'id',
                        'prescription_id',
                        'patient_id',
                        'status',
                        'requested_at',
                    ]
                ]
            ]);

        $renewals = $response->json('renewals');
        $this->assertCount(3, $renewals);
    }

    public function test_doctor_can_approve_renewal()
    {
        $renewal = PrescriptionRenewal::factory()->create([
            'prescription_id' => $this->prescription->id,
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->medecinUser)
            ->postJson("/api/prescription-renewals/{$renewal->id}/approve", [
                'medecin_notes' => 'Traitement approuvé pour 3 mois supplémentaires',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'renewal',
                'new_prescription',
            ]);

        // Check renewal status updated
        $this->assertDatabaseHas('prescription_renewals', [
            'id' => $renewal->id,
            'status' => 'approved',
            'processed_by' => $this->medecin->id,
        ]);

        // Check renewals_used incremented
        $this->prescription->refresh();
        $this->assertEquals(1, $this->prescription->renewals_used);

        // Check new prescription created
        $this->assertEquals(2, Prescription::count());
    }

    public function test_doctor_can_reject_renewal()
    {
        $renewal = PrescriptionRenewal::factory()->create([
            'prescription_id' => $this->prescription->id,
            'medecin_id' => $this->medecin->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->medecinUser)
            ->postJson("/api/prescription-renewals/{$renewal->id}/reject", [
                'medecin_notes' => 'Nécessite une nouvelle consultation pour réévaluation',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('prescription_renewals', [
            'id' => $renewal->id,
            'status' => 'rejected',
            'processed_by' => $this->medecin->id,
        ]);

        // Check renewals_used NOT incremented
        $this->prescription->refresh();
        $this->assertEquals(0, $this->prescription->renewals_used);

        // Check no new prescription created
        $this->assertEquals(1, Prescription::count());
    }

    public function test_patient_can_get_renewal_history()
    {
        PrescriptionRenewal::factory()->count(3)->create([
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
        ]);

        $response = $this->actingAs($this->patientUser)
            ->getJson('/api/prescription-renewals/history');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'renewals' => [
                    '*' => [
                        'id',
                        'prescription_id',
                        'status',
                        'requested_at',
                    ]
                ]
            ]);

        $renewals = $response->json('renewals');
        $this->assertCount(3, $renewals);
    }
}
