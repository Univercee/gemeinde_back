<?php
namespace App\Managers;

class UsersManager
{
    public static function get($id) {
        $user = app('db')->select("SELECT * FROM users WHERE id = :id", ['id' => $id]);
        return empty($user) ? null : $user[0];
    }

    //TODO: Move to TelegramAuthController
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

    // [GENA-7]
    public static function getByKey($key) {
      $user = app('db')->select("SELECT * FROM users WHERE verification_key = :k", ['k' => $key]);
      return empty($user) ? null : $user[0];
    }

    // [GENA-7]
    public static function getByEmail($email) {
      $user = app('db')->select("SELECT * FROM users WHERE email = :email", ['email' => $email]);
      return empty($user) ? null : $user[0];
    }

    // [GENA-7]
    public static function add($email) {
      $key = bin2hex(random_bytes(32));
      app('db')->insert("INSERT INTO users (email, verification_key, verification_key_expires_at)
                        VALUES(?, ?, NOW() + INTERVAL 1 DAY)",
                        [$email, $key]);
      return $key;
    }

    // [GENA-7]
    public static function setVerificationKey($email) {
      $key = uniqid();
      app('db')->update("UPDATE users
                    SET verification_key = :k, verification_key_expires_at = NOW() + INTERVAL 5 MINUTE
                    WHERE email = :email",
                    ['email' => $email, 'k' => $key]);
      return $key;
    }
}
