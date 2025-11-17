<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            AppointmentSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('Database seeding completed successfully!');
        $this->command->info('========================================');
        $this->command->info('');
        $this->command->info('Test Accounts:');
        $this->command->info('');
        $this->command->info('Admin:');
        $this->command->info('  Email: admin@sehadigital.tn');
        $this->command->info('  Password: admin123');
        $this->command->info('');
        $this->command->info('Patients:');
        $this->command->info('  Email: patient1@example.tn (or patient2, patient3)');
        $this->command->info('  Password: password123');
        $this->command->info('');
        $this->command->info('Doctors:');
        $this->command->info('  Email: dr.amira@example.tn (or other doctors)');
        $this->command->info('  Password: doctor123');
        $this->command->info('');
    }
}
