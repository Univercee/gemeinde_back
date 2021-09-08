<?php
namespace App\Managers\Queues;

use WeStacks\TeleBot\Laravel\TeleBot;

class TelegramQueueManager extends QueueManager{

    protected string $queue_table = "telegram_queue";
    protected array $queue_columns = ["id", "user_id", "event_id", "telegram_id", "deliver_at", "sent_at", "body"];
    protected string $queue_channel = "Telegram";

    //implements
    function send(array $message): Void
    {
        TeleBot::sendMessage([
            'chat_id' =>  $message["telegram_id"],
            'text'    =>  $message["body"]
        ]);
    }
}
?>