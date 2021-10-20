<?php

namespace App\Console\Commands;

use App\Managers\Queues\EmailQueueManager;
use Illuminate\Console\Command;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gena:send_emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends messages from email queue';

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
        $eqm = new EmailQueueManager;
        if($eqm->queueLength()){
            $eqm->consumeQueue();
        }
    }
}
