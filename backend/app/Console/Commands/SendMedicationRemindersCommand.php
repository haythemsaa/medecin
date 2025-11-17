<?php

namespace App\Console\Commands;

use App\Jobs\SendMedicationReminders;
use Illuminate\Console\Command;

class SendMedicationRemindersCommand extends Command
{
    protected $signature = 'reminders:send-medications';
    protected $description = 'Send medication reminders to users';

    public function handle(): int
    {
        $this->info('Sending medication reminders...');

        SendMedicationReminders::dispatch();

        $this->info('Medication reminders job dispatched successfully!');

        return Command::SUCCESS;
    }
}
