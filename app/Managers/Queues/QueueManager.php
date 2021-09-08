<?php
namespace App\Managers\Queues;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

abstract class QueueManager{

  protected string $queue_table;    // name of queue table in database
  protected array $queue_columns;   // columns of queue table in database 
  protected string $queue_channel;  // name of channel (how it saving in database)
  protected abstract function send(array $message): void; 

  
  //
  public function consumeQueue(): bool
  {
    $messages = DB::table($this->queue_table)
      ->get($this->queue_columns)
      ->whereNull("sent_at")
      ->where("deliver_at", "<=", now());
    $messages = json_decode(json_encode($messages), true); //convert stdClass to array
    return $this->sendAll($messages);
  }


  //return count of added rows
  public function addtoQueue(array $messages): Int
  {
    $messages = array_filter($messages, function($message){
        return $message['channel'] == $this->queue_channel;
    });
    if(empty($messages)){
      return 0;
    }

    $messages = array_values($messages);
    $keys = array_keys($messages[0]);
    $valid_keys = array_intersect($keys, $this->queue_columns);

    $messages = array_map(function($message) use($keys, $valid_keys){
      foreach($keys as $key){
        if(!in_array($key, $valid_keys)) unset($message[$key]);
      }
      return $message;
    }, $messages);
    
    return DB::table($this->queue_table)->insertOrIgnore($messages);
  }


  //
  public function queueLength(): Int
  {
    $responce = app('db')->select("SELECT COUNT(*) AS count FROM ".$this->queue_table." WHERE sent_at IS NULL");
    return $responce[0]->count;
  }


  //
  public function sentCount(int $interval = 1): Int
  {
    $responce = app('db')->select("SELECT COUNT(*) AS count FROM ".$this->queue_table." WHERE sent_at BETWEEN NOW() - INTERVAL ".$interval." DAY AND NOW()");
    return $responce[0]->count;
  }


  //
  protected function sendAll(array $messages): bool
  {
    $success = true;
    foreach($messages as $message)
    {
      try
      {
        $this->send($message);
      }
      catch(\Exception $e)
      {
        $success = false;
        Log::error('QUEUE_ERROR: Message[ID '.$message->id.'] not sent'."\n".'Exception:'."\n".$e);
        continue;
      }

      app('db')->update("UPDATE ".$this->queue_table."
                        SET sent_at = NOW()
                        WHERE id = :id",
                        ['id' => $message["id"]]);
    }
    return $success;
  }
}
?>