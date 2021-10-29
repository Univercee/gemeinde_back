<?php

namespace App\Console;

use App\Managers\Services\GgamaurServiceManager;
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
        Commands\GenerateGgamaurEvents::class,
        
        Commands\DispatchCalendarEvents::class,
        Commands\DispatchSwisscomEvents::class,
        Commands\DispatchGgamaurEvents::class,

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
        $schedule->command("generate:ggamaur")->everyMinute();
        $schedule->command("generate:garbage_calendar")->everyMinute();
        $schedule->command("generate:swisscom")->everyMinute();

        $schedule->command("dispatch:ggamaur")->everyMinute();
        $schedule->command("dispatch:garbage_calendar")->everyMinute();
        $schedule->command("dispatch:swisscom")->everyMinute();

        $schedule->command("send:emails")->everyMinute()->withoutOverlapping();
        $schedule->command("send:telegram")->everyMinute()->withoutOverlapping();
    }
}
