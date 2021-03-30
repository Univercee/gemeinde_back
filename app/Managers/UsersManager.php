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

    public static function getChannels($user_id){
      $channels = app('db')->select("SELECT email, telegram_username FROM users
                              WHERE id = :user_id",
                              ['user_id' => $user_id]);
      return $channels[0];
    }

    public static function setChannelVerificationKey($id, $email) {
      $key = uniqid();
      app('db')->update("UPDATE users
                    SET verification_key = :k, verification_key_expires_at = NOW() + INTERVAL 1 HOUR, email_buffer = :email
                    WHERE id = :id",
                    ['id' => $id, 'k' => $key, 'email' => $email]);
      return $key;
    }

    public static function deleteEmailChannel($user_id){
      $channels = UsersManager::getChannels($user_id);
      var_dump($channels);
      if($channels->telegram_username){
        app('db')->delete("UPDATE users
                          SET email = null
                          WHERE id = :user_id",
                          ['user_id' => $user_id]);
      }
    }

    public static function deleteTgChannel($user_id){
      $channels = UsersManager::getChannels($user_id);
      if($channels->email){
        app('db')->delete("UPDATE users
                          SET telegram_id = null, telegram_username = null
                          WHERE id = :user_id",
                          ['user_id' => $user_id]);
      }
    }

    public static function setPersonalDetails($user_id, $firstname, $lastname, $language){
      $firstname = trim($firstname);
      $lastname = trim($lastname);
      $firstname = ($firstname == "") ? null : $firstname;
      $lastname = ($lastname == "") ? null : $lastname;
      $language = ($language == 'en' || $language == 'de') ? $language : 'en';
      app('db')->update("UPDATE users
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
      app('db')->update("UPDATE user_locations
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
      app('db')->insert("INSERT INTO user_locations(user_id, title, location_id, street_name, street_number)
                        VALUES(?, ?, ?, ?, ?)",
                        [$user_id, $title, $location_id, $street_name, $street_number]);
    }

    public static function deleteUserLocation($user_id, $user_location_id){
      app('db')->delete("DELETE FROM user_locations
                        WHERE user_id = :user_id AND id = :id",
                        ['id' => $user_location_id,
                        'user_id' => $user_id]);
    }

}
