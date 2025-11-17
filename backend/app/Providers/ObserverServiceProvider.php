<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Medecin;
use App\Models\Review;
use App\Observers\AppointmentObserver;
use App\Observers\MedecinObserver;
use App\Observers\ReviewObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Medecin::observe(MedecinObserver::class);
        Appointment::observe(AppointmentObserver::class);
        Review::observe(ReviewObserver::class);
    }
}
