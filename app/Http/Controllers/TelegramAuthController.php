<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
define('BOT_TOKEN', env('BOT_TOKEN'));

class TelegramAuthController extends Controller{
    
    // [GENA-9]
    // code from https://gist.github.com/anonymous/6516521b1fb3b464534fbc30ea3573c2
    public function authentication(Request $request) {  
        $auth_data = $request['auth_data'];
        $check_hash = $auth_data['hash'];
        unset($auth_data['hash']);
        $hash = $this->getAuthHash($auth_data);
        if (strcmp($hash, $check_hash) !== 0) {
            return response()->json(['error' => 'Data is NOT from Telegram'], 400);
        }
        if ((time() - $auth_data['auth_date']) > 86400) {
            return response()->json(['error' => 'Data is outdated'], 400);
        }
        $user = $this->getUserByTelegramId($auth_data['id']);
        if(empty($user)){
            $first_name = $auth_data['first_name'] ?? null;
            $last_name = $auth_data['last_name'] ?? null;
            $username = $auth_data['username'] ?? null;
            $this->confirmRegistration($auth_data['id'], $first_name, $last_name, $username);
            return response()->json(['message' => 'User has been registered'], 200);
        }
        else{
            $this->confirmLogin($auth_data['id']);
            return response()->json(['message' => 'User authorized'], 200);
        }
    }   

    // [GENA-9]
    private function confirmRegistration($telegram_id, $first_name, $last_name, $username){
        app('db')->insert("INSERT INTO users(telegram_id, first_name, last_name, username, auth_type)
                            VALUES(?,?,?,?,?)",
                            [$telegram_id, $first_name, $last_name, $username, 'TG']);
    }

    // [GENA-9]
    private function confirmLogin($telegram_id){
        app('db')->update("UPDATE users
                            SET users.auth_type = 'TG'
                            WHERE users.telegram_id = :telegram_id",
                            ['telegram_id'=>$telegram_id]);
    }

    // [GENA-9]
    private function getAuthHash($auth_data){
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
    private function getUserByTelegramId($telegram_id){
        $user = app('db')->select("SELECT telegram_id
                                    FROM users
                                    WHERE telegram_id=:telegram_id",['telegram_id'=>$telegram_id]);
        return empty($user) ? $user : $user[0];
    }
}

?>