<?php
namespace App\Managers;
use WeStacks\TeleBot\Laravel\TeleBot;
use Illuminate\Support\Facades\Log;
class TelegramManager
{
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

  public static function consumeQueue(){
    $messages = app('db')->select("SELECT id, body, telegram_id FROM telegram_queue
                                WHERE sent_at IS NULL AND deliver_at >= NOW()");
    $errors = false;
    foreach($messages as $message){
      try{
        TelegramManager::send($message->telegram_id, $message->body);
      }catch(\Exception $e){
        $errors = true;
        Log::error('TELEGRAM_QUEUE_ERROR: Message[ID '.$message->id.'] not sent to User[TELEGRAM_ID '.$message->telegram_id.']'."\n".'Exception:'."\n".$e);
        continue;
      }
      app('db')->update("UPDATE telegram_queue
                        SET sent_at = NOW()
                        WHERE id = :id",
                        ['id' => $message->id]);
    }
    return !$errors;
  }

  public static function send($telegram_id, $body){
    return TeleBot::sendMessage([
      'chat_id' =>  $telegram_id,
      'text'    =>  $body
    ]);
  }
}

?>