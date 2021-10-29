<?php

namespace App\Console\Commands;

use App\Managers\Queues\TelegramQueueManager;
use Illuminate\Console\Command;

class SendTelegram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:telegram';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends mesages from telegram queue';

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
        $tqm = new TelegramQueueManager;
        if($tqm->queueLength()){
            $tqm->consumeQueue();
        }
    }
}
