<?php

namespace App\Console\Commands;

use App\Managers\Events\EventManager;
use App\Managers\Services\GarbageServiceManager;
use App\Managers\Services\GgamaurServiceManager;
use App\Managers\Services\ServiceFactory;
use Illuminate\Console\Command;

class GenerateGgamaurEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:ggamaur';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Produces ggamaur message queues';

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
        ServiceFactory::ggamaur()->addEvents();
    }
}
