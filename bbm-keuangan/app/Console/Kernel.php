<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('generate:persediaan')
        //     ->dailyAt('21:00');
        // $schedule->command('generate:labarugi')
        //     ->dailyAt('21:00');
        // $schedule->command('generate:labarugibulanan')
        //     ->lastDayOfMonth('21:30');

        $schedule->command('generate:persediaan')
        ->dailyAt('21:00');
        $schedule->command('generate:labarugi')
        ->dailyAt('21:30');
        $schedule->command('generate:labarugibulanan')
        ->lastDayOfMonth('21:50');

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
