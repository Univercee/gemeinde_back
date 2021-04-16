<?php
namespace App\Managers;
use Illuminate\Support\Facades\Mail;
use App\Mail\GarbageCalendarMail;
use Illuminate\Support\Facades\Log;
class EmailManager
{

    public static function consumeQueue(){
        $messages = app('db')->select("SELECT id, user_id, body, subject, email FROM email_queue
                                    WHERE sent_at IS NULL AND deliver_at >= NOW()");
        $errors = false;
        foreach($messages as $message){
          try{
            self::send($message->email, $message->subject, $message->body);
          }catch(\Exception $e){
            $errors = true;
            Log::error('EMAIL_QUEUE_ERROR: Message[ID '.$message->id.'] not sent to Email['.$message->email.']'."\n".'Exception:'."\n".$e);
            continue;
          }
          app('db')->update("UPDATE email_queue
                            SET sent_at = NOW()
                            WHERE id = :id",
                            ['id' => $message->id]);
        }
        return !$errors;
      }
    
      public static function send($email, $subject, $body){
        Mail::to($email)->send(new GarbageCalendarMail($subject, $body));
      }
}
?>