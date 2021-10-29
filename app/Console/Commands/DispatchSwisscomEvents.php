<?php

namespace App\Console\Commands;

use App\Managers\Events\EventManager;
use App\Managers\Services\SwisscomServiceManager;
use Illuminate\Console\Command;

class DispatchSwisscomEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dispatch:swisscom';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Produces swisscom message queues';

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
        EventManager::dispatch(new SwisscomServiceManager);
    }
}
