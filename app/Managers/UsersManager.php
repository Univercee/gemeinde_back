<?php
namespace App\Managers;

use App\Mail\UserWelcomeMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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
      app('db')->insert("INSERT INTO users (email_pending, verification_key, verification_key_expires_at)
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
        'telegram_username' => $username,
        'avatar' => $avatar,]
    );
    return SessionsManager::generateSessionKey($id);
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
                            users.verification_key_expires_at = null,
                            users.verification_key = null,
                            users.avatar = :avatar
                        WHERE users.id = :id",['id'=>$id, 'avatar'=>$avatar]);
    Mail::to($email)->send(new UserWelcomeMail(null));
    return SessionsManager::generateSessionKey($id);
  }

  // [GENA-7]
  public static function confirmLoginEmail($id){
    app('db')->update("UPDATE users
                        SET users.verification_key_expires_at = null,
                            users.verification_key = null
                        WHERE users.id = :id",['id'=>$id]);
    return SessionsManager::generateSessionKey($id);
  }

  // [GENA-7]
  public static function onLinkExpire($id){
    app('db')->update("UPDATE users
                        SET users.verification_key_expires_at = null,
                            users.verification_key = null
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

    public static function getChannels($user_id){
      $channels = app('db')->select("SELECT email as email, telegram_username as tg FROM users
                              WHERE id = :user_id",
                              ['user_id' => $user_id]);
      return $channels[0];
    }

    public static function setChannelVerificationKey($id, $email) {
      $key = uniqid();
      app('db')->update("UPDATE users
                    SET verification_key = :k, verification_key_expires_at = NOW() + INTERVAL 1 HOUR, email_pending = :email
                    WHERE id = :id",
                    ['id' => $id, 'k' => $key, 'email' => $email]);
      return $key;
    }

    public static function deleteEmailChannel($user_id){
      $channels = UsersManager::getChannels($user_id);
      if($channels->tg){
        return app('db')->delete("UPDATE users
                          SET email = null
                          WHERE id = :user_id",
                          ['user_id' => $user_id]);
      }
      return false;
    }

    public static function deleteTgChannel($user_id){
      $channels = UsersManager::getChannels($user_id);
      if($channels->email){
        return app('db')->delete("UPDATE users
                          SET telegram_id = null, telegram_username = null
                          WHERE id = :user_id",
                          ['user_id' => $user_id]);
      }
      return false;
    }

    public static function setPersonalDetails($user_id, $firstname, $lastname, $language){
      $firstname = trim($firstname);
      $lastname = trim($lastname);
      $firstname = ($firstname == "") ? null : $firstname;
      $lastname = ($lastname == "") ? null : $lastname;
      $language = ($language == 'en' || $language == 'de') ? $language : 'en';
      return app('db')->update("UPDATE users
                          SET first_name = :firstname, last_name = :lastname, language = :language
                          WHERE id = :user_id",
                          ['firstname' => $firstname, 'lastname' => $lastname, 'language' => $language, 'user_id' => $user_id]);
    }

    public static function getPersonalDetails($user_id){
      $personal_details = app('db')->select("SELECT first_name, last_name, language FROM users
                                      WHERE id = :user_id",
                                      ['user_id' => $user_id]);
      return empty($personal_details) ? null : $personal_details[0];
    }

    public static function getUserLocations($user_id){
      $user_locations = app('db')->select("SELECT id, location_id, title, street_name, street_number FROM user_locations
                                          WHERE user_id = :user_id",
                                          ['user_id' => $user_id]);
      return $user_locations;
    }

    public static function setUserLocation($user_id, $user_location_id, $location_id, $title, $street_name, $street_number){
      $title = trim($title);
      $street_name = trim($street_name);
      $street_number = trim($street_number);
      return app('db')->update("UPDATE user_locations
                        SET title = :title, location_id = :location_id, street_name = :street_name, street_number = :street_number
                        WHERE user_id = :user_id AND id = :id",
                        ['title' => $title,
                        'location_id' => $location_id,
                        'street_name' => $street_name,
                        'street_number' => $street_number,
                        'id' => $user_location_id,
                        'user_id' => $user_id]);
    }

    public static function addUserLocation($user_id, $location_id, $title, $street_name, $street_number){
      $title = trim($title);
      $street_name = trim($street_name);
      $street_number = trim($street_number);
      return app('db')->insert("INSERT INTO user_locations(user_id, title, location_id, street_name, street_number)
                        VALUES(?, ?, ?, ?, ?)",
                        [$user_id, $title, $location_id, $street_name, $street_number]);
    }

    public static function deleteUserLocation($user_id, $user_location_id){
      return app('db')->delete("DELETE FROM user_locations
                        WHERE user_id = :user_id AND id = :id",
                        ['id' => $user_location_id,
                        'user_id' => $user_id]);
    }


}
