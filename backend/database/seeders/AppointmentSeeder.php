<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Payment;
use App\Models\Patient;
use App\Models\Medecin;
use App\Models\MedicalConsent;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patients = Patient::all();
        $medecins = Medecin::where('validation_status', 'validated')->get();

        if ($patients->isEmpty() || $medecins->isEmpty()) {
            $this->command->warn('No patients or validated doctors found. Run UserSeeder first.');
            return;
        }

        // Create past appointments (completed)
        for ($i = 0; $i < 10; $i++) {
            $patient = $patients->random();
            $medecin = $medecins->random();

            $appointmentDate = now()->subDays(rand(1, 30))->setHour(rand(9, 17))->setMinute([0, 15, 30, 45][rand(0, 3)]);

            $appointment = Appointment::create([
                'patient_id' => $patient->id,
                'medecin_id' => $medecin->id,
                'appointment_date' => $appointmentDate,
                'duration' => 30,
                'appointment_type' => collect(['video', 'phone'])->random(),
                'reason' => collect([
                    'Consultation de routine',
                    'Suivi médical',
                    'Renouvellement d\'ordonnance',
                    'Problème de santé',
                ])->random(),
                'status' => 'completed',
                'notes' => 'Rendez-vous effectué avec succès',
            ]);

            // Create payment
            Payment::create([
                'appointment_id' => $appointment->id,
                'amount' => $medecin->consultation_price,
                'payment_method' => collect(['card', 'e-dinar', 'mobile'])->random(),
                'payment_status' => 'paid',
                'transaction_id' => 'TXN' . now()->timestamp . rand(1000, 9999),
            ]);

            // Create medical consent
            MedicalConsent::create([
                'patient_id' => $patient->id,
                'medecin_id' => $medecin->id,
                'appointment_id' => $appointment->id,
                'granted_at' => $appointmentDate,
                'expires_at' => $appointmentDate->copy()->addDays(30),
            ]);

            // Create consultation
            Consultation::create([
                'appointment_id' => $appointment->id,
                'medecin_id' => $medecin->id,
                'chief_complaint' => collect([
                    'Douleurs abdominales',
                    'Fièvre et fatigue',
                    'Toux persistante',
                    'Maux de tête',
                ])->random(),
                'examination' => 'Examen clinique normal',
                'diagnosis' => collect([
                    'Gastrite',
                    'Syndrome grippal',
                    'Bronchite aiguë',
                    'Céphalées de tension',
                ])->random(),
                'treatment_plan' => 'Prescription médicamenteuse et repos',
                'vital_signs' => [
                    'blood_pressure_systolic' => rand(110, 130),
                    'blood_pressure_diastolic' => rand(70, 85),
                    'heart_rate' => rand(60, 80),
                    'temperature' => rand(365, 375) / 10,
                ],
                'started_at' => $appointmentDate,
                'ended_at' => $appointmentDate->copy()->addMinutes(30),
            ]);
        }

        // Create upcoming appointments (confirmed)
        for ($i = 0; $i < 5; $i++) {
            $patient = $patients->random();
            $medecin = $medecins->random();

            $appointmentDate = now()->addDays(rand(1, 14))->setHour(rand(9, 17))->setMinute([0, 15, 30, 45][rand(0, 3)]);

            $appointment = Appointment::create([
                'patient_id' => $patient->id,
                'medecin_id' => $medecin->id,
                'appointment_date' => $appointmentDate,
                'duration' => 30,
                'appointment_type' => collect(['video', 'phone'])->random(),
                'reason' => collect([
                    'Première consultation',
                    'Consultation de suivi',
                    'Avis spécialisé',
                ])->random(),
                'status' => 'confirmed',
            ]);

            // Create payment
            Payment::create([
                'appointment_id' => $appointment->id,
                'amount' => $medecin->consultation_price,
                'payment_method' => collect(['card', 'e-dinar', 'mobile'])->random(),
                'payment_status' => 'paid',
                'transaction_id' => 'TXN' . now()->timestamp . rand(1000, 9999),
            ]);

            // Create medical consent
            MedicalConsent::create([
                'patient_id' => $patient->id,
                'medecin_id' => $medecin->id,
                'appointment_id' => $appointment->id,
                'granted_at' => now(),
                'expires_at' => $appointmentDate->copy()->addDays(30),
            ]);
        }

        // Create pending appointments
        for ($i = 0; $i < 3; $i++) {
            $patient = $patients->random();
            $medecin = $medecins->random();

            $appointmentDate = now()->addDays(rand(15, 30))->setHour(rand(9, 17))->setMinute([0, 15, 30, 45][rand(0, 3)]);

            Appointment::create([
                'patient_id' => $patient->id,
                'medecin_id' => $medecin->id,
                'appointment_date' => $appointmentDate,
                'duration' => 30,
                'appointment_type' => collect(['video', 'phone'])->random(),
                'reason' => 'Consultation en attente de confirmation',
                'status' => 'pending',
            ]);
        }

        $this->command->info('Appointments seeded successfully!');
        $this->command->info('- 10 completed appointments with consultations');
        $this->command->info('- 5 upcoming confirmed appointments');
        $this->command->info('- 3 pending appointments');
    }
}
