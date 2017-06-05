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
        //
        Commands\PluginEodDataWriteCommand::class,
        Commands\PluginEodDataResetCommand::class,
        Commands\PluginIntradayDataResetCommand::class,
        Commands\PluginIntradayDataWriteCommand::class,
        Commands\RemoveDuplicateEodCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('plugin:writeLastEod')->dailyAt('15:00')->emailOutputTo('fazalmohammad19@gmail.com');
        $schedule->command('plugin:writeLastIntra')->dailyAt('20:00')->emailOutputTo('fazalmohammad19@gmail.com');

       /* $schedule->call(function () {
            $i=rand(1,100);
            echo $i;
        })->everyMinute()->emailOutputTo('fazalmohammad19@gmail.com');*/

    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
