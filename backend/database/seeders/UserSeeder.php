<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Patient;
use App\Models\Medecin;
use App\Models\MedicalRecord;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'email' => 'admin@sehadigital.tn',
            'password' => Hash::make('admin123'),
            'first_name' => 'Admin',
            'last_name' => 'Seha Digital',
            'phone' => '20123456',
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create Test Patients
        $patients = [
            [
                'email' => 'patient1@example.tn',
                'first_name' => 'Ahmed',
                'last_name' => 'Ben Salem',
                'phone' => '21234567',
                'cin' => '12345678',
                'date_of_birth' => '1990-05-15',
                'gender' => 'male',
                'governorate' => 'Tunis',
                'health_coverage_type' => 'cnam',
            ],
            [
                'email' => 'patient2@example.tn',
                'first_name' => 'Fatima',
                'last_name' => 'Trabelsi',
                'phone' => '22345678',
                'cin' => '23456789',
                'date_of_birth' => '1985-08-22',
                'gender' => 'female',
                'governorate' => 'Sfax',
                'health_coverage_type' => 'mutuelle',
            ],
            [
                'email' => 'patient3@example.tn',
                'first_name' => 'Mohamed',
                'last_name' => 'Gharbi',
                'phone' => '23456789',
                'cin' => '34567890',
                'date_of_birth' => '1995-12-10',
                'gender' => 'male',
                'governorate' => 'Sousse',
                'health_coverage_type' => 'none',
            ],
        ];

        foreach ($patients as $patientData) {
            $user = User::create([
                'email' => $patientData['email'],
                'password' => Hash::make('password123'),
                'first_name' => $patientData['first_name'],
                'last_name' => $patientData['last_name'],
                'phone' => $patientData['phone'],
                'role' => 'patient',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            $patient = Patient::create([
                'user_id' => $user->id,
                'cin' => $patientData['cin'],
                'date_of_birth' => $patientData['date_of_birth'],
                'gender' => $patientData['gender'],
                'address' => fake()->streetAddress(),
                'governorate' => $patientData['governorate'],
                'postal_code' => fake()->postcode(),
                'health_coverage_type' => $patientData['health_coverage_type'],
                'health_coverage_number' => $patientData['health_coverage_type'] === 'cnam' ? fake()->numerify('#############') : null,
            ]);

            // Create medical record for each patient
            MedicalRecord::create([
                'patient_id' => $patient->id,
                'blood_type' => collect(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->random(),
                'allergies' => fake()->randomElement([
                    ['Pénicilline', 'Arachides'],
                    ['Pollen'],
                    [],
                ]),
                'chronic_diseases' => fake()->randomElement([
                    ['Diabète type 2'],
                    ['Hypertension'],
                    [],
                ]),
                'current_treatments' => fake()->randomElement([
                    ['Metformine 500mg - 2x/jour'],
                    [],
                ]),
                'vaccinations' => [
                    'COVID-19 (3 doses - 2023)',
                    'Grippe saisonnière (2024)',
                ],
            ]);
        }

        // Create Test Doctors
        $specialties = [
            'Médecine générale',
            'Cardiologie',
            'Pédiatrie',
            'Dermatologie',
            'Gynécologie-Obstétrique',
            'Psychiatrie',
        ];

        $governorates = ['Tunis', 'Ariana', 'Sfax', 'Sousse', 'Nabeul', 'Monastir'];

        $doctors = [
            [
                'email' => 'dr.amira@example.tn',
                'first_name' => 'Amira',
                'last_name' => 'Jebali',
                'phone' => '24567890',
                'speciality' => 'Médecine générale',
                'governorate' => 'Tunis',
                'consultation_price' => 60,
                'years_experience' => 10,
            ],
            [
                'email' => 'dr.karim@example.tn',
                'first_name' => 'Karim',
                'last_name' => 'Bouazizi',
                'phone' => '25678901',
                'speciality' => 'Cardiologie',
                'governorate' => 'Sfax',
                'consultation_price' => 100,
                'years_experience' => 15,
            ],
            [
                'email' => 'dr.salma@example.tn',
                'first_name' => 'Salma',
                'last_name' => 'Hamdi',
                'phone' => '26789012',
                'speciality' => 'Pédiatrie',
                'governorate' => 'Sousse',
                'consultation_price' => 70,
                'years_experience' => 8,
            ],
            [
                'email' => 'dr.mehdi@example.tn',
                'first_name' => 'Mehdi',
                'last_name' => 'Ayari',
                'phone' => '27890123',
                'speciality' => 'Dermatologie',
                'governorate' => 'Tunis',
                'consultation_price' => 80,
                'years_experience' => 12,
            ],
            [
                'email' => 'dr.ines@example.tn',
                'first_name' => 'Ines',
                'last_name' => 'Khiari',
                'phone' => '28901234',
                'speciality' => 'Gynécologie-Obstétrique',
                'governorate' => 'Ariana',
                'consultation_price' => 90,
                'years_experience' => 14,
            ],
        ];

        foreach ($doctors as $index => $doctorData) {
            $user = User::create([
                'email' => $doctorData['email'],
                'password' => Hash::make('doctor123'),
                'first_name' => $doctorData['first_name'],
                'last_name' => $doctorData['last_name'],
                'phone' => $doctorData['phone'],
                'role' => 'medecin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            Medecin::create([
                'user_id' => $user->id,
                'speciality' => $doctorData['speciality'],
                'numero_ordre' => sprintf('%04d/2020', 1000 + $index),
                'consultation_price' => $doctorData['consultation_price'],
                'years_experience' => $doctorData['years_experience'],
                'governorate' => $doctorData['governorate'],
                'cabinet_address' => fake()->streetAddress() . ', ' . $doctorData['governorate'],
                'cabinet_phone' => '7' . fake()->numerify('#######'),
                'bio' => 'Médecin spécialisé en ' . $doctorData['speciality'] . ' avec ' . $doctorData['years_experience'] . ' ans d\'expérience.',
                'languages_spoken' => ['ar', 'fr'],
                'diplomas' => [
                    'Doctorat en Médecine - Faculté de Médecine de Tunis',
                    'Spécialisation en ' . $doctorData['speciality'],
                ],
                'validation_status' => 'validated',
                'rating' => fake()->randomFloat(1, 4.0, 5.0),
                'total_consultations' => fake()->numberBetween(50, 500),
            ]);
        }

        $this->command->info('Users seeded successfully!');
        $this->command->info('Admin: admin@sehadigital.tn / admin123');
        $this->command->info('Patients: patient1@example.tn / password123 (and patient2, patient3)');
        $this->command->info('Doctors: dr.amira@example.tn / doctor123 (and others)');
    }
}
