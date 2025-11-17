<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Patient;
use App\Models\Medecin;
use App\Models\Appointment;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    private User $patientUser;
    private User $medecinUser;
    private Patient $patient;
    private Medecin $medecin;
    private Appointment $completedAppointment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->patientUser = User::factory()->create(['role' => 'patient']);
        $this->patient = Patient::factory()->create(['user_id' => $this->patientUser->id]);

        $this->medecinUser = User::factory()->create(['role' => 'medecin']);
        $this->medecin = Medecin::factory()->create(['user_id' => $this->medecinUser->id]);

        $this->completedAppointment = Appointment::factory()->create([
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
            'status' => 'completed',
            'date' => Carbon::yesterday(),
        ]);
    }

    /** @test */
    public function patient_can_create_review_for_completed_appointment()
    {
        $response = $this->actingAs($this->patientUser)
            ->postJson('/api/reviews', [
                'appointment_id' => $this->completedAppointment->id,
                'rating_professionalism' => 5,
                'rating_listening' => 5,
                'rating_explanation' => 4,
                'rating_punctuality' => 5,
                'rating_effectiveness' => 5,
                'comment' => 'Excellent médecin, très à l\'écoute',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Avis ajouté avec succès',
            ]);

        $this->assertDatabaseHas('reviews', [
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
            'appointment_id' => $this->completedAppointment->id,
            'overall_rating' => 4.8, // Average of 5, 5, 4, 5, 5
        ]);
    }

    /** @test */
    public function cannot_review_non_completed_appointment()
    {
        $pendingAppointment = Appointment::factory()->create([
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($this->patientUser)
            ->postJson('/api/reviews', [
                'appointment_id' => $pendingAppointment->id,
                'rating_professionalism' => 5,
                'rating_listening' => 5,
                'rating_explanation' => 5,
                'rating_punctuality' => 5,
                'rating_effectiveness' => 5,
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Seuls les rendez-vous terminés peuvent être évalués',
            ]);
    }

    /** @test */
    public function cannot_create_duplicate_review()
    {
        Review::factory()->create([
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
            'appointment_id' => $this->completedAppointment->id,
        ]);

        $response = $this->actingAs($this->patientUser)
            ->postJson('/api/reviews', [
                'appointment_id' => $this->completedAppointment->id,
                'rating_professionalism' => 5,
                'rating_listening' => 5,
                'rating_explanation' => 5,
                'rating_punctuality' => 5,
                'rating_effectiveness' => 5,
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Vous avez déjà évalué ce rendez-vous',
            ]);
    }

    /** @test */
    public function patient_can_update_review_within_7_days()
    {
        $review = Review::factory()->create([
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
            'appointment_id' => $this->completedAppointment->id,
            'created_at' => Carbon::now()->subDays(3),
        ]);

        $response = $this->actingAs($this->patientUser)
            ->putJson("/api/reviews/{$review->id}", [
                'rating_professionalism' => 4,
                'rating_listening' => 4,
                'rating_explanation' => 4,
                'rating_punctuality' => 4,
                'rating_effectiveness' => 4,
                'comment' => 'Updated review',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Avis mis à jour avec succès',
            ]);
    }

    /** @test */
    public function cannot_update_review_after_7_days()
    {
        $review = Review::factory()->create([
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
            'appointment_id' => $this->completedAppointment->id,
            'created_at' => Carbon::now()->subDays(8),
        ]);

        $response = $this->actingAs($this->patientUser)
            ->putJson("/api/reviews/{$review->id}", [
                'rating_professionalism' => 4,
                'rating_listening' => 4,
                'rating_explanation' => 4,
                'rating_punctuality' => 4,
                'rating_effectiveness' => 4,
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'La période de modification est expirée (7 jours)',
            ]);
    }

    /** @test */
    public function can_view_medecin_reviews()
    {
        Review::factory()->count(5)->create([
            'medecin_id' => $this->medecin->id,
        ]);

        $response = $this->getJson("/api/reviews/medecins/{$this->medecin->id}");

        $response->assertStatus(200)
            ->assertJsonCount(5, 'reviews');
    }

    /** @test */
    public function review_requires_all_rating_criteria()
    {
        $response = $this->actingAs($this->patientUser)
            ->postJson('/api/reviews', [
                'appointment_id' => $this->completedAppointment->id,
                'rating_professionalism' => 5,
                // Missing other ratings
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'rating_listening',
                'rating_explanation',
                'rating_punctuality',
                'rating_effectiveness',
            ]);
    }

    /** @test */
    public function ratings_must_be_between_1_and_5()
    {
        $response = $this->actingAs($this->patientUser)
            ->postJson('/api/reviews', [
                'appointment_id' => $this->completedAppointment->id,
                'rating_professionalism' => 6, // Invalid
                'rating_listening' => 5,
                'rating_explanation' => 5,
                'rating_punctuality' => 5,
                'rating_effectiveness' => 5,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['rating_professionalism']);
    }
}
