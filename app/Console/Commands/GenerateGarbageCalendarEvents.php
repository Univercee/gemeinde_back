<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Managers\Services\GarbageServiceManager;

class GenerateGarbageCalendarEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gena:generate_garbage_calendar_events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates upcomming events from all entries of Garbage Calendar';

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
        $gsm = new GarbageServiceManager();
        $gsm->addEvents();
    }
}
