<?php
namespace App\Managers;

use Illuminate\Support\Facades\DB;

class UsersManager
{
    public static function get($id) {
        $user = app('db')->select("SELECT * FROM users WHERE id = :id", ['id' => $id]);
        return empty($user) ? null : $user[0];
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

  public static function setVerificationRegistrationKey($email) {
    $key = uniqid();
    app('db')->update("UPDATE users
                    SET verification_key = :k, verification_key_expires_at = NOW() + INTERVAL 1 DAY
                    WHERE email = :email",
      ['email' => $email, 'k' => $key]);
    return $key;
  }


  // [GENA-9]
  public static function confirmRegistration($auth_data){
    $first_name = $auth_data['first_name'] ?? null;
    $last_name = $auth_data['last_name'] ?? null;
    $username = $auth_data['username'] ?? null;
    $avatar = $auth_data['photo_url'] ?? null;
    $id = DB::table('users')->insertGetId(
      ['telegram_id' => $auth_data['id'],
        'first_name' => $first_name,
        'last_name' => $last_name,
        'username' => $username,
        'avatar' => $avatar,
        'auth_type' => 'TG']
    );
    return SessionsManager::generateSessionKey($id);
  }


  // [GENA-9]
  public static function confirmLogin($telegram_id, $user_id){
    app('db')->update("UPDATE users
                            SET users.auth_type = 'TG'
                            WHERE users.telegram_id = :telegram_id",
      ['telegram_id'=>$telegram_id]);
    return SessionsManager::generateSessionKey($user_id);
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

  // [GENA-9]
  public static function getUserByTelegramId($telegram_id){
    $user = app('db')->select("SELECT id,telegram_id
                                    FROM users
                                    WHERE telegram_id=:telegram_id",['telegram_id'=>$telegram_id]);
    return empty($user) ? $user : $user[0];
  }

///////////////////

  // [GENA-7]
  public static function confirmRegistrationEmail($id, $email){

    $avatar = AvatarsManager::getGravatar($email);
    if(!$avatar) $avatar = AvatarsManager::getAvataaars();
    app('db')->update("UPDATE users
                        SET registered_at = NOW(),
                            users.key_until = null,
                            users.secretkey = null,
                            users.avatar = :avatar,
                            users.auth_type = 'E'
                        WHERE users.id = :id",['id'=>$id, 'avatar'=>$avatar]);
    //TODO: send WelcomeMail
    return SessionsManager::generateSessionKey($id);
  }

  // [GENA-7]
  public static function confirmLoginEmail($id){
    app('db')->update("UPDATE users
                        SET users.key_until = null,
                            users.secretkey = null,
                            users.auth_type = 'E'
                        WHERE users.id = :id",['id'=>$id]);
    return SessionsManager::generateSessionKey($id);
  }

  // [GENA-7]
  public static function onLinkExpire($id){
    app('db')->update("UPDATE users
                        SET users.key_until = null,
                            users.secretkey = null
                        WHERE users.id = :id",['id'=>$id]);
  }
  public static function waitFiveMin($user): bool
  {
    $wait = false;
    $timePlus = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." -5 minutes"));
    $timeVer = date("Y-m-d H:i:s",strtotime(date($user->verification_key_expires_at)." -1 day"));
    if($timePlus < $timeVer) {
    $wait = true;
    }
    return $wait;
  }
}
