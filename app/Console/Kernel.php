<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        /*Commands\Inspire::class,
        Commands\DriverDepositeRequest::class, 
        Commands\StoreRideMapImage::class*/
    ];

    
    
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /*$schedule->command('inspire') ->hourly();
        $schedule->command('driver_deposite:schedule')->daily();
        $schedule->command('store_ride_map_image:schedule')->hourly();*/
    }
}    