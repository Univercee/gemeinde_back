<?php
namespace App\Managers\Queues;

class QueueFactory{

    //
    public static function email()
    {
        return new EmailQueueManager();
    }

    //
    public static function telegram()
    {
        return new TelegramQueueManager();
    }

}