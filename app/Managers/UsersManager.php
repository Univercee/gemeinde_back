<?php
namespace App\Managers;

use App\Mail\UserWelcomeMail;
use Exception;
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

    public static function getByTgId($telegram_id) {
      $user = app('db')->select("SELECT * FROM users WHERE telegram_id = :telegram_id", ['telegram_id' => $telegram_id]);
      return empty($user) ? null : $user[0];
    }

    // [GENA-7]
    public static function add($email, $lang) {
      $key = random_int(100000, 999999);
      app('db')->insert("INSERT INTO users (email_pending, verification_key, verification_key_expires_at, language)
                        VALUES(?, ?, NOW() + INTERVAL 1 DAY, ?)",
                        [$email, $key, $lang]);
      return $key;
    }

    // [GENA-7]
    public static function setVerificationKey($email) {
      $key = random_int(100000, 999999);
      app('db')->update("UPDATE users
                    SET verification_key = :k, verification_key_expires_at = NOW() + INTERVAL 5 MINUTE
                    WHERE email = :email",
                    ['email' => $email, 'k' => $key]);
      return $key;
    }


  public static function setVerificationRegistrationKey($email) {
    $key = random_int(100000, 999999);
    app('db')->update("UPDATE users
                    SET verification_key = :k, verification_key_expires_at = NOW() + INTERVAL 1 DAY
                    WHERE email = :email",
      ['email' => $email, 'k' => $key]);
    return $key;
  }


  // [GENA-9]
  public static function confirmRegistration($auth_data, $ip_address){
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
    return SessionsManager::generateSessionKey($id, $ip_address);
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
  public static function confirmRegistrationEmail($id, $email, $ip_address){

    $avatar = AvatarsManager::getGravatar($email);
    if(!$avatar) $avatar = AvatarsManager::getAvataaars();
    app('db')->update("UPDATE users
                        SET registered_at = NOW(),
                            users.verification_key_expires_at = null,
                            users.verification_key = null,
                            users.avatar = :avatar,
                            users.email = users.email_pending,
                            users.email_pending = null
                        WHERE users.id = :id",['id'=>$id, 'avatar'=>$avatar]);
    Mail::to($email)->send(new UserWelcomeMail(null));
    return SessionsManager::generateSessionKey($id, $ip_address);
  }

  // [GENA-7]
  public static function confirmLoginEmail($id, $ip_address){
    app('db')->update("UPDATE users
                        SET users.verification_key_expires_at = null,
                            users.verification_key = null
                        WHERE users.id = :id",['id'=>$id]);
    return SessionsManager::generateSessionKey($id, $ip_address);
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
        return 
        app('db')->delete(
          "UPDATE users
          SET email = null
          WHERE id = :user_id",
          ['user_id' => $user_id]
        )
        &&
        app('db')->delete(
          "DELETE uls
          FROM user_location_services uls
          JOIN user_locations ul ON ul.id = uls.user_location_id
          WHERE ul.user_id = :user_id
          AND uls.channel = 'Email'",
          ["user_id" => $user_id]
        );
      }
      return false;
    }

    public static function deleteTgChannel($user_id){
      $channels = UsersManager::getChannels($user_id);
      if($channels->email){
        return 
        app('db')->delete(
          "UPDATE users
          SET telegram_id = null, telegram_username = null
          WHERE id = :user_id",
          ['user_id' => $user_id]
        )
        &&
        app('db')->delete(
          "DELETE uls
          FROM user_location_services uls
          JOIN user_locations ul ON ul.id = uls.user_location_id
          WHERE ul.user_id = :user_id
          AND uls.channel = 'Telegram'",
          ["user_id" => $user_id]
        );
      }
      return false;
    }

    public static function setPersonalDetails($user_id, $firstname, $lastname, $language){
      try{
        $firstname = trim($firstname);
        $lastname = trim($lastname);
        $firstname = ($firstname == "") ? null : $firstname;
        $lastname = ($lastname == "") ? null : $lastname;
        $language = ($language == 'en' || $language == 'de') ? $language : 'en';
        app('db')->update("UPDATE users
                          SET first_name = :firstname, last_name = :lastname, language = :language
                          WHERE id = :user_id",
                          ['firstname' => $firstname, 'lastname' => $lastname, 'language' => $language, 'user_id' => $user_id]);
        return true;
      }
      catch(Exception $e){
        return false;
      }
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
      foreach($user_locations as $location){
        $location->services = UsersManager::getUserServices($user_id, $location->location_id, $location->id);
      }                                         
      return $user_locations;
    }

    public static function setUserLocation($user_id, $user_location_id, $location_id, $title, $street_name, $street_number){
      try{
        $title = trim($title);
        $street_name = trim($street_name);
        $street_number = trim($street_number);
        app('db')->update("UPDATE user_locations
                        SET title = :title, location_id = :location_id, street_name = :street_name, street_number = :street_number
                        WHERE user_id = :user_id AND id = :id",
                        ['title' => $title,
                        'location_id' => $location_id,
                        'street_name' => $street_name,
                        'street_number' => $street_number,
                        'id' => $user_location_id,
                        'user_id' => $user_id]);
        return true;
      }
      catch(Exception $e){
        return false;
      }
    }

    public static function addUserLocation($user_id, $location_id, $title, $street_name, $street_number, $services){
      $title = trim($title);
      $street_name = trim($street_name);
      $street_number = trim($street_number);
      $id = DB::table('user_locations')->insertGetId(['user_id'=>$user_id,
                                                      'title'=>$title, 
                                                      'location_id'=>$location_id, 
                                                      'street_name'=>$street_name,
                                                      'street_number'=>$street_number]);
      if($id){
        UsersManager::setUserServices($services, $id);
        return $id;
      }
      return false;                                     
    }

    public static function deleteUserLocation($user_id, $user_location_id){
      return app('db')->delete("DELETE FROM user_locations
                        WHERE user_id = :user_id AND id = :id",
                        ['id' => $user_location_id,
                        'user_id' => $user_id]);
    }

    public static function getUserServices($user_id, $locationId, $user_location_id){
      return app('db')->select("SELECT ls.service_id, s.name_en as name, channel, uls.frequency
                                    FROM location_services AS ls
                                    JOIN services s ON s.id = ls.service_id
                                    JOIN locations l ON l.id = ls.location_id AND l.id = :location_id
                                    LEFT JOIN user_locations ul ON ul.location_id = l.id AND user_id = :user_id AND ul.id = :user_location_id
                                    LEFT JOIN user_location_services uls ON uls.user_location_id = ul.id AND uls.service_id =s.id",
                                  ['user_id' => $user_id,
                                  'location_id' => $locationId,
                                  'user_location_id' => $user_location_id]);
    }

    public static function setUserServices($services, $user_location_id){
      for($i=0; $i<count($services); $i++){
        unset($services[$i]['name']);
        $services[$i]['user_location_id'] = $user_location_id;
      }
      DB::table('user_location_services')->where('user_location_id', $user_location_id)->delete();
      
      //'== count($services)' because upsert function return number of updated/inserted rows
      return DB::table('user_location_services')->upsert(
        $services,
        ['user_location_id', 'service_id'], ['channel', 'frequency']
      ) == count($services);
    }
}
