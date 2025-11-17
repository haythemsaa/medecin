<?php

namespace Tests\Feature;

use App\Models\Medecin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeolocationTest extends TestCase
{
    use RefreshDatabase;

    protected User $medecinUser;
    protected Medecin $medecin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create medecin in Tunis
        $this->medecinUser = User::factory()->create(['role' => 'medecin']);
        $this->medecin = Medecin::factory()->create([
            'user_id' => $this->medecinUser->id,
            'status' => 'validated',
            'latitude' => 36.8065,
            'longitude' => 10.1815,
            'ville' => 'Tunis',
            'code_postal' => '1000',
            'show_on_map' => true,
        ]);
    }

    public function test_can_search_nearby_doctors()
    {
        $response = $this->getJson('/api/geolocation/nearby', [
            'latitude' => 36.8065,
            'longitude' => 10.1815,
            'radius' => 10,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'doctors' => [
                    '*' => [
                        'id',
                        'user',
                        'specialite',
                        'latitude',
                        'longitude',
                        'distance',
                        'ville',
                    ]
                ],
                'center' => [
                    'latitude',
                    'longitude',
                ],
                'radius',
                'total',
            ]);
    }

    public function test_nearby_search_calculates_distance_correctly()
    {
        // Create another doctor 5km away (approximate)
        Medecin::factory()->create([
            'user_id' => User::factory()->create(['role' => 'medecin']),
            'status' => 'validated',
            'latitude' => 36.85,
            'longitude' => 10.20,
            'show_on_map' => true,
        ]);

        $response = $this->getJson('/api/geolocation/nearby', [
            'latitude' => 36.8065,
            'longitude' => 10.1815,
            'radius' => 10,
        ]);

        $doctors = $response->json('doctors');

        foreach ($doctors as $doctor) {
            $this->assertArrayHasKey('distance', $doctor);
            $this->assertLessThanOrEqual(10, $doctor['distance']);
        }
    }

    public function test_nearby_search_respects_radius()
    {
        // Create doctor far away (Sfax - ~200km from Tunis)
        Medecin::factory()->create([
            'user_id' => User::factory()->create(['role' => 'medecin']),
            'status' => 'validated',
            'latitude' => 34.7406,
            'longitude' => 10.7603,
            'ville' => 'Sfax',
            'show_on_map' => true,
        ]);

        $response = $this->getJson('/api/geolocation/nearby', [
            'latitude' => 36.8065,
            'longitude' => 10.1815,
            'radius' => 10, // Only 10km radius
        ]);

        $doctors = $response->json('doctors');

        // Should only find the Tunis doctor, not Sfax
        $this->assertLessThanOrEqual(1, count($doctors));
    }

    public function test_nearby_search_can_filter_by_specialty()
    {
        $this->medecin->update(['specialite' => 'Cardiologie']);

        Medecin::factory()->create([
            'user_id' => User::factory()->create(['role' => 'medecin']),
            'status' => 'validated',
            'specialite' => 'Pédiatrie',
            'latitude' => 36.81,
            'longitude' => 10.19,
            'show_on_map' => true,
        ]);

        $response = $this->getJson('/api/geolocation/nearby', [
            'latitude' => 36.8065,
            'longitude' => 10.1815,
            'radius' => 10,
            'specialty' => 'Cardiologie',
        ]);

        $doctors = $response->json('doctors');

        foreach ($doctors as $doctor) {
            $this->assertEquals('Cardiologie', $doctor['specialite']);
        }
    }

    public function test_nearby_search_sorts_by_distance()
    {
        // Create doctors at different distances
        Medecin::factory()->create([
            'user_id' => User::factory()->create(['role' => 'medecin']),
            'status' => 'validated',
            'latitude' => 36.82,
            'longitude' => 10.19,
            'show_on_map' => true,
        ]);

        Medecin::factory()->create([
            'user_id' => User::factory()->create(['role' => 'medecin']),
            'status' => 'validated',
            'latitude' => 36.81,
            'longitude' => 10.17,
            'show_on_map' => true,
        ]);

        $response = $this->getJson('/api/geolocation/nearby', [
            'latitude' => 36.8065,
            'longitude' => 10.1815,
            'radius' => 10,
        ]);

        $doctors = $response->json('doctors');

        // Check that doctors are sorted by distance (ascending)
        for ($i = 0; $i < count($doctors) - 1; $i++) {
            $this->assertLessThanOrEqual(
                $doctors[$i + 1]['distance'],
                $doctors[$i]['distance']
            );
        }
    }

    public function test_can_search_doctors_by_city()
    {
        // Create doctors in different cities
        Medecin::factory()->create([
            'user_id' => User::factory()->create(['role' => 'medecin']),
            'status' => 'validated',
            'ville' => 'Sfax',
            'show_on_map' => true,
        ]);

        $response = $this->getJson('/api/geolocation/by-city', [
            'ville' => 'Tunis',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'doctors',
                'ville',
                'total',
            ]);

        $doctors = $response->json('doctors');

        foreach ($doctors as $doctor) {
            $this->assertStringContainsString('Tunis', $doctor['ville']);
        }
    }

    public function test_city_search_can_filter_by_specialty()
    {
        $this->medecin->update(['specialite' => 'Dermatologie']);

        $response = $this->getJson('/api/geolocation/by-city', [
            'ville' => 'Tunis',
            'specialty' => 'Dermatologie',
        ]);

        $doctors = $response->json('doctors');

        foreach ($doctors as $doctor) {
            $this->assertEquals('Dermatologie', $doctor['specialite']);
        }
    }

    public function test_can_get_popular_cities()
    {
        // Create doctors in different cities
        Medecin::factory()->count(5)->create([
            'user_id' => User::factory()->create(['role' => 'medecin']),
            'status' => 'validated',
            'ville' => 'Tunis',
        ]);

        Medecin::factory()->count(3)->create([
            'user_id' => User::factory()->create(['role' => 'medecin']),
            'status' => 'validated',
            'ville' => 'Sfax',
        ]);

        $response = $this->getJson('/api/geolocation/popular-cities');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'cities' => [
                    '*' => [
                        'ville',
                        'count',
                    ]
                ]
            ]);

        $cities = $response->json('cities');

        // Check sorted by count
        for ($i = 0; $i < count($cities) - 1; $i++) {
            $this->assertGreaterThanOrEqual(
                $cities[$i + 1]['count'],
                $cities[$i]['count']
            );
        }
    }

    public function test_doctor_can_update_location()
    {
        $response = $this->actingAs($this->medecinUser)
            ->postJson('/api/geolocation/update', [
                'latitude' => 36.85,
                'longitude' => 10.20,
                'ville' => 'Ariana',
                'code_postal' => '2080',
                'show_on_map' => false,
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('medecins', [
            'id' => $this->medecin->id,
            'latitude' => 36.85,
            'longitude' => 10.20,
            'ville' => 'Ariana',
            'code_postal' => '2080',
            'show_on_map' => false,
        ]);
    }

    public function test_doctors_with_show_on_map_false_are_excluded()
    {
        $this->medecin->update(['show_on_map' => false]);

        $response = $this->getJson('/api/geolocation/nearby', [
            'latitude' => 36.8065,
            'longitude' => 10.1815,
            'radius' => 10,
        ]);

        $doctors = $response->json('doctors');

        // Should not find the hidden doctor
        $doctorIds = array_column($doctors, 'id');
        $this->assertNotContains($this->medecin->id, $doctorIds);
    }

    public function test_only_validated_doctors_appear_in_search()
    {
        $this->medecin->update(['status' => 'pending']);

        $response = $this->getJson('/api/geolocation/nearby', [
            'latitude' => 36.8065,
            'longitude' => 10.1815,
            'radius' => 10,
        ]);

        $doctors = $response->json('doctors');

        // Should not find non-validated doctors
        $doctorIds = array_column($doctors, 'id');
        $this->assertNotContains($this->medecin->id, $doctorIds);
    }
}
