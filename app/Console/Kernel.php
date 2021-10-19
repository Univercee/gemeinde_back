<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\GenerateGarbageCalendarEvents::class,
        Commands\GenerateSwisscomEvents::class,
        Commands\DispatchCalendarEvents::class,
        Commands\DispatchSwisscomEvents::class,
        Commands\SendEmails::class,
        Commands\SendTelegram::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command("gena:generate_garbage_calendar_events")->everyMinute();
        $schedule->command("gena:generate_swisscom_events")->everyMinute();

        $schedule->command("gena:dispatch_calendar_events")->everyMinute();
        $schedule->command("gena:dispatch_swisscom_events")->everyMinute();

        $schedule->command("gena:send_emails")->everyMinute()->withoutOverlapping()->between('8:00','20:00');
        $schedule->command("gena:send_telegram")->everyMinute()->withoutOverlapping()->between('8:00','20:00');
    }
}
