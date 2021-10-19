<?php

namespace App\Console\Commands;

use App\Managers\Events\EventManager;
use App\Managers\Services\GarbageServiceManager;
use Illuminate\Console\Command;

class DispatchCalendarEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gena:dispatch_calendar_events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Produces garbage calendar message queues';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        EventManager::dispatch(new GarbageServiceManager);
    }
}
