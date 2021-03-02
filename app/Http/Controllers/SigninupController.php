<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
define('BOT_TOKEN', env('BOT_TOKEN')); // place bot token of your bot here

class SigninupController extends Controller
{

    // [GENA-7]
    private function plusTime($time){ //+24 hours, +5 minutes
        $keyAtTime = date("Y-m-d h:i:s");
        $endTimeConvert = strtotime($time, strtotime($keyAtTime));
        return $endTime = date('Y-m-d h:i:s', $endTimeConvert);
    }

    // [GENA-7]
    public function signinupflow(Request $request){
        $email = $request->input('email');
        if(!preg_match("/^[-!#-'*+\/-9=?^-~]+(?:\.[-!#-'*+\/-9=?^-~]+)*@[-!#-'*+\/-9=?^-~]+(?:\.[-!#-'*+\/-9=?^-~]+)+$/i", $email)) {
            return response()->json(['error' => 'Bad Request'], 400);
        }
        //check if robot
        $response = Http::asForm()
            ->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => '6LehX2caAAAAAEL3F-LR8H8bdaqX9_1uKTdopO9U',
                'response' => $request->input('token'),
            ])->json();

        $score = (float)json_encode(floatval($response['score']));
        if($score < 0.5) {
            return response()->json(['Error'=> 'You are robot, dont event try!!!!'], 400);
        }
        $queryCheckUser = app('db')->select("SELECT email, registered_at FROM users WHERE email = :email", ['email' => $email]);
        if(empty($queryCheckUser)){
            $endTime =  $this->plusTime("+24 hours");
            $secretKey = uniqid();
            $addUser = app('db')->insert("INSERT INTO users
                        (email, first_name, last_name, username, secretkey, key_until, auth_type)
                        values(?, ?, ?, ?, ?, ?, 'E')",
                [$email, null, null, null, $secretKey, $endTime]);

            $send = ['key'=>$secretKey];
            Mail::to($email)->send(new WelcomeMail($send));
            return response()->json(['message' => 'Email sent'], 200);
        }
        $registered_at = $queryCheckUser[0]->registered_at;
        if($registered_at != null){
            $endTime = $this->plusTime("+5 minutes");
            $secretKey = uniqid();
            $updateUser = app('db')->update("UPDATE users
                SET secretkey = '$secretKey' , key_until = '$endTime' , registered_at ='$registered_at'
                WHERE email = :email AND registered_at = :registered_at",
                ['email' => $queryCheckUser[0]->email, 'registered_at' => $registered_at]);
            $send = ['key'=>$secretKey];
            Mail::to($email)->send(new WelcomeMail($send));
            Mail::mailer('log')->to($email)->send(new WelcomeMail($send));
            return response()->json(['message' => 'Email sent'], 200);
        } else {
            $endTime =  $this->plusTime("+24 hours");
            $secretKey = uniqid();
            $updateUser = app('db')->update("UPDATE users
                               SET secretkey = '$secretKey', key_until = '$endTime'
                               WHERE email = :email", ['email'=> $queryCheckUser[0]->email]);
            $send = ['key'=>$secretKey];
            Mail::to($email)->send(new WelcomeMail($send));
            Mail::mailer('log')->to($email)->send(new WelcomeMail(['key'=>$secretKey]));
            return response()->json(['message' => 'Email sent'], 200);
        }
        return response()->json(['Error' => 'Bad Request'], 400);
    }

    // [GENA-7]
    public function verifyKey($key){
        $user = $this->getUserByKey($key);
        if(empty($user)){
            return response()->json(['error' => 'Not found'], 404);
        }

        if(strtotime($user->key_until) < time()){
            $this->onLinkExpire($user->id);
            return response()->json(['error' => 'Key has expired'], 403);
        }else{
            if(is_null($user->registered_at)){
                $this->confirmRegistration($user->id);
                return response()->json(['message' => 'User has been registered'], 200);
            }
            else{
                $this->confirmLogin($user->id);
                return response()->json(['message' => 'User authorized'], 200);
            }
        }
    }

    // [GENA-7]
    private function getUserByKey($key){
        $user = app('db')->select("SELECT id, email, key_until, registered_at FROM users
                                WHERE users.secretkey = :k",['k'=>$key]);
        return empty($user) ? $user : $user[0];
    }

    // [GENA-7]
    private function confirmRegistration($id){
        app('db')->update("UPDATE users
                        SET registered_at = NOW(),
                            users.key_until = null,
                            users.secretkey = null
                        WHERE users.id = :id",['id'=>$id]);
    }

    // [GENA-7]
    private function confirmLogin($id){
        app('db')->update("UPDATE users
                        SET users.key_until = null,
                            users.secretkey = null
                        WHERE users.id = :id",['id'=>$id]);
    }

    // [GENA-7]
    private function onLinkExpire($id){
        app('db')->update("UPDATE users
                        SET users.key_until = null,
                            users.secretkey = null
                        WHERE users.id = :id",['id'=>$id]);
    }



    // [GENA-9]
    // code from https://gist.github.com/anonymous/6516521b1fb3b464534fbc30ea3573c2
    public function checkTelegramAuthorization(Request $request) {    
        $auth_data = $request['auth_data'];
        $check_hash = $auth_data['hash'];
        unset($auth_data['hash']);
        $data_check_arr = [];
        foreach ($auth_data as $key => $value) {
          $data_check_arr[] = $key . '=' . $value;
        }
        sort($data_check_arr);
        $data_check_string = implode("\n", $data_check_arr);
        $secret_key = hash('sha256', BOT_TOKEN, true);
        $hash = hash_hmac('sha256', $data_check_string, $secret_key);
        if (strcmp($hash, $check_hash) !== 0) {
            return response()->json(['error' => 'Data is NOT from Telegram'], 400);
        }
        if ((time() - $auth_data['auth_date']) > 86400) {
            return response()->json(['error' => 'Data is outdated'], 400);
        }
        return $this->confirmTelegramAuthorization($auth_data);
    }   

    // [GENA-9]
    private function confirmTelegramAuthorization($auth_data) {
        $user = $this->getUserByTelegramId($auth_data['id']);
        if(empty($user)){
            $first_name = $auth_data['first_name'] ?? null;
            $last_name = $auth_data['last_name'] ?? null;
            $username = $auth_data['username'] ?? null;
            $this->confirmTelegramRegistration($auth_data['id'], $first_name, $last_name, $username);
            return response()->json(['message' => 'User has been registered'], 200);
        }
        else{
            return response()->json(['message' => 'User authorized'], 200);
        }
    }

    // [GENA-9]
    private function getUserByTelegramId($telegram_id){
        $user = app('db')->select("SELECT telegram_id
                                    FROM users
                                    WHERE telegram_id=:telegram_id",['telegram_id'=>$telegram_id]);
        return empty($user) ? $user : $user[0];
    }

    // [GENA-9]
    private function confirmTelegramRegistration($telegram_id, $first_name, $last_name, $username){
        app('db')->insert("INSERT INTO users(telegram_id, first_name, last_name, username)
                            VALUES(?,?,?,?)",[$telegram_id, $first_name, $last_name, $username]);
    }
}
