<?php

namespace App\Console\Commands;

use App\Managers\Services\ServiceFactory;
use Illuminate\Console\Command;

class GenerateSwisscomEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gena:generate_swisscom_events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pulls the events from swisscom API and pushes it into DB';

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
        ServiceFactory::swisscom()->addEvents();
    }
}
