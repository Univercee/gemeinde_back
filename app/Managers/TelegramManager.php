<?php
namespace App\Managers;
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
}

?>