<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;

class SigninupController extends Controller
{
    private function plusTime($time){ //+24 hours, +5 minutes
        $keyAtTime = date("Y-m-d h:i:s");
        $endTimeConvert = strtotime($time, strtotime($keyAtTime));
        return $endTime = date('Y-m-d h:i:s', $endTimeConvert);
    }
    public function signinupflow(Request $request){
        $email = $request->input('email');
        if(!preg_match("/^[-!#-'*+\/-9=?^-~]+(?:\.[-!#-'*+\/-9=?^-~]+)*@[-!#-'*+\/-9=?^-~]+(?:\.[-!#-'*+\/-9=?^-~]+)+$/i", $email)) {
            return response()->json(['error' => 'Bad Request'], 400);
        }
        $queryCheckUser = app('db')->select("SELECT * FROM users WHERE email = :email", ['email' => $email]);
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
           // $token = $request->input('token');
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
            //$token = $request->input('token');
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
/* for testing needs
    public function confirm($key){
        $selectAllUsers = app('db')->select("SELECT * FROM users WHERE secretkey = :key", ['key'=>$key]);
        if($selectAllUsers) {
            if ($selectAllUsers[0]->key_until > date("Y-m-d h:i:s")) { // check expiration
                if ($selectAllUsers[0]->registered_at == null) { //check if not registered
                    $timeNow = date("Y-m-d h:i:s");
                    $updateUser = app('db')->update("UPDATE users
                        SET secretkey = NULL , key_until = NULL , registered_at ='$timeNow'
                        WHERE email = :email AND secretkey = :secretkey",
                        ['email' => $selectAllUsers[0]->email, 'secretkey' => $selectAllUsers[0]->secretkey]);
                    return response()->json(['Message'=>'Successfuly loged in!']);
                }else{
                     return response()->json(['Message'=>'Successfuly loged in! registered already']);
                }
            }else{
                    return response()->json(['Error'=>'Key is expired']);
            }
        }else{
            return response()->json(['Error'=>'Key is expired or doesnt exist']);
        }

    }*/
}

