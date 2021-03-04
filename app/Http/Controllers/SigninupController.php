<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;

class SigninupController extends Controller
{

    // [GENA-7]
    private function plusTime($time){ //+24 hours, +5 minutes
        $keyAtTime = date("Y-m-d H:i:s");
        $endTimeConvert = strtotime($time, strtotime($keyAtTime));
        return $endTime = date('Y-m-d H:i:s', $endTimeConvert);
    }

    // [GENA-7]
    public function signinupflow(Request $request){
        $email = $request->input('email');
        if(!preg_match("/^[-!#-'*+\/-9=?^-~]+(?:\.[-!#-'*+\/-9=?^-~]+)*@[-!#-'*+\/-9=?^-~]+(?:\.[-!#-'*+\/-9=?^-~]+)+$/i", $email)) {
            return response()->json(['error' => 'Bad Request Email'], 400);
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
            if(!$addUser){
                return $response-json(['Error'=>'INSERT SQL ERROR'],400);
            }
            $send = ['key'=>$secretKey];
            Mail::to($email)->send(new WelcomeMail($send));
            return response()->json(['message' => 'Email sent'], 200);
        }
        $registered_at = $queryCheckUser[0]->registered_at;
        if($registered_at != null){
            $endTime = $this->plusTime("+5 min");
            $secretKey = uniqid();
            $updateUser = app('db')->update("UPDATE users
                SET secretkey = '$secretKey' , key_until = '$endTime' , registered_at ='$registered_at'
                WHERE email = :email AND registered_at = :registered_at",
                ['email' => $queryCheckUser[0]->email, 'registered_at' => $registered_at]);
            if(!$updateUser){
                return $response-json(['Error'=>'UPDATE SQL ERROR'],400);
            }
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
            if(!$updateUser){
                return $response-json(['Error'=>'UPDATE SQL ERROR'],400);
            }
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
}
