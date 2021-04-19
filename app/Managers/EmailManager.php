<?php
namespace App\Managers;
use Illuminate\Support\Facades\Mail;
use App\Mail\ServiceNotificationMail;
use Illuminate\Support\Facades\Log;
use App\Interfaces\ChannelManagerInterface;
use phpDocumentor\Reflection\Types\Boolean;

class EmailManager implements ChannelManagerInterface
{
    private static $queueTableName = 'email_queue';
    //implements
    public static function getQueueTable(): String{
      return self::$queueTableName;
    }
    
    //implements
    public static function consumeQueue(): Bool{
        $messages = app('db')->select("SELECT id, user_id, body, \"subject\", template, email FROM ".self::getQueueTable()."
                                    WHERE sent_at IS NULL AND deliver_at <= NOW() ORDER BY deliver_at LIMIT 50");
        $success = true;
        foreach($messages as $message){
          try{
            self::send($message->email, $message->body, $message->subject, $message->template);
          }catch(\Exception $e){
            $success = false;
            Log::error('EMAIL_QUEUE_ERROR: Message[ID '.$message->id.'] not sent to Email['.$message->email.']'."\n".'Exception:'."\n".$e);
            continue;
          }
          app('db')->update("UPDATE email_queue
                            SET sent_at = NOW()
                            WHERE id = :id",
                            ['id' => $message->id]);
        }
        return $success;
      }
    
      //implements
      public static function send($identifier, $body, $subject = null, $template = null): Void{
        Mail::to($identifier)->send(new ServiceNotificationMail($template, $subject, $body));
      }

      //implements
      public static function queueLength(): Int{
        $responce = app('db')->select("SELECT COUNT(*) AS count FROM ".self::getQueueTable()." WHERE sent_at IS NULL");
        return $responce[0]->count;
      }

      //implements
      public static function sentCount($interval = 1): Int{
        $responce = app('db')->select("SELECT COUNT(*) AS count FROM ".self::getQueueTable()." WHERE sent_at BETWEEN NOW() - INTERVAL ".$interval." DAY AND NOW()");
        return $responce[0]->count;
      }
}
?>