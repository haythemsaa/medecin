<?php

namespace App\Events;

use App\Models\Medecin;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MedecinValidated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Medecin $medecin;
    public string $status;
    public ?string $notes;

    /**
     * Create a new event instance.
     */
    public function __construct(Medecin $medecin, string $status, ?string $notes = null)
    {
        $this->medecin = $medecin;
        $this->status = $status;
        $this->notes = $notes;
    }
}
