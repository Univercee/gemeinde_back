<?php
namespace App\Managers\Queues;

use Illuminate\Support\Facades\Mail;
use App\Mail\ServiceNotificationMail;

class EmailQueueManager extends QueueManager{

    protected string $queue_table = "email_queue";
    protected array $queue_columns = ["id", "user_id", "event_id", "email", "deliver_at", "sent_at", "title", "body", "template_id"];
    protected string $queue_channel = "Email";
    
    //implements
    function send(array $message): void
    {
        Mail::to($message["email"])->send(new ServiceNotificationMail($message["template_id"], $message["title"], $message["body"]));
    }
}
?>