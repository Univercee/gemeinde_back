<?php


namespace App\Managers;


class AvatarsManager
{
  public function setter($key){
    $user = app('db')->select("SELECT user_id FROM sessions WHERE session_key = :key", ["key"=> $key]);
    return $user[0]->user_id;
  }


}
