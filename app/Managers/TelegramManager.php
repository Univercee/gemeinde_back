<?php
namespace App\Managers;
use WeStacks\TeleBot\Laravel\TeleBot;
use Illuminate\Support\Facades\Log;
use App\Interfaces\ChannelManagerInterface;
class TelegramManager implements ChannelManagerInterface
{
  private static $queueTableName = 'telegram_queue';

  //implements
  public static function getQueueTable(): String{
    return self::$queueTableName;
  }

  //implements
  public static function consumeQueue(){
    $messages = app('db')->select("SELECT id, user_id, body, telegram_id FROM ".self::getQueueTable()."
                                  WHERE sent_at IS NULL AND deliver_at <= NOW() ORDER BY deliver_at LIMIT 50");
    $success = true;
    foreach($messages as $message){
      try{
        self::send($message->telegram_id, $message->body);
      }catch(\Exception $e){
        $success = true;
        Log::error('TELEGRAM_QUEUE_ERROR: Message[ID '.$message->id.'] not sent to User[TELEGRAM_ID '.$message->telegram_id.']'."\n".'Exception:'."\n".$e);
        continue;
      }
      app('db')->update("UPDATE telegram_queue
                        SET sent_at = NOW()
                        WHERE id = :id",
                        ['id' => $message->id]);
    }
    return $success;
  }
  
  //implements
  public static function send($identifier, $body, $subject = null, $template = null): Void{
    TeleBot::sendMessage([
      'chat_id' =>  $identifier,
      'text'    =>  $body
    ]);
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

  public static function checkHash($auth_data, $check_hash){
    $isValid = false;
    $hash = TelegramManager::getAuthHash($auth_data);
    if (strcmp($hash, $check_hash) === 0) {
        $isValid = true;
    }
    return $isValid;
  }

  public static function getAuthHash($auth_data) {
    $data_check_arr = [];
    foreach ($auth_data as $key => $value) {
      $data_check_arr[] = $key . '=' . $value;
    }
    sort($data_check_arr);
    $data_check_string = implode("\n", $data_check_arr);
    $secret_key = hash('sha256', BOT_TOKEN, true);
    $hash = hash_hmac('sha256', $data_check_string, $secret_key);
    return $hash;
  }
}

?>