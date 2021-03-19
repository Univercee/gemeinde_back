<?php

namespace App\Managers;


use Illuminate\Support\Facades\Http;

class UsersManager{
    public static function getUserInfo($user_id){
        $userEmail = app('db')->select("SELECT * FROM users WHERE id = :id",['id'=>$user_id]);
        return $userEmail[0];
    }
    public static function getAuthHash($auth_data){
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
    public static function getRecaptcha($token){
      $response = Http::asForm()
        ->post('https://www.google.com/recaptcha/api/siteverify', [
          'secret' => env('GOOGLE_RECAPTCHA_SECRET_KEY'),
          'response' => $token,
        ])->json();
      return (float)json_encode(floatval($response['score']));
    }
  public static function getUserByKey($key){
    $user = app('db')->select("SELECT * FROM users
                                WHERE secretkey = :k",['k'=>$key]);
    return empty($user) ? $user : $user[0];
  }
  public static function addUser($email, $userId){
    $secretKey = uniqid();
    app('db')->UPDATE("UPDATE users
                            SET email = :email, secretkey = '$secretKey', key_until = NOW() + INTERVAL 24 HOUR
                            WHERE id = $userId", ['email' => $email]);
    return ['key'=>$secretKey];
  }
}
