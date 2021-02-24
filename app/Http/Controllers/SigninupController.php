<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;

class SigninupController extends Controller
{
    public function plusTime($time){ //+24 hours, +5 minutes
        $keyAtTime = date("Y-m-d h:i:s");
        $endTimeConvert = strtotime($time, strtotime($keyAtTime));
        return $endTime = date('Y-m-d h:i:s', $endTimeConvert);
    }
    public function signinupflow(Request $request){
    Mail::to('ahmed.abdull2016@gmail.com')->send(new WelcomeMail('Test mail from SwiftMailer'));
    Mail::mailer('log')->to('ahmed.abdull2016@gmail.com')->send(new WelcomeMail('Test mail from SwiftMailer'));

        $email = $request->input('email');
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            return response()->json(['error' => 'Bad Email'], 400);
        }
        $queryCheckUser = app('db')->select("SELECT * FROM users WHERE email = :email", ['email' => $email]);
        if(empty($queryCheckUser)){

            $endTime =  $this->plusTime("+24 hours");
            $secretKey = uniqid();
            $addUser = app('db')->insert("INSERT INTO users
                        (email, first_name, last_name, username, sercretkey, key_at)
                        values(?, ?, ?, ?, ?, ?)",
                [$email, null, null, null, $secretKey, $endTime]);

            return response()->json(['key' => $secretKey]);
        }
        $registered_at = $queryCheckUser[0]->registered_at;
        if($registered_at != null){

            $endTime = $this->plusTime("+5 minutes");
            $secretKey = uniqid();
            $updateUser = app('db')->update("UPDATE users
                SET sercretkey = '$secretKey' , key_at = '$endTime' , registered_at ='$registered_at'
                WHERE email = :email AND registered_at = :registered_at",
                ['email' => $queryCheckUser[0]->email, 'registered_at' => $registered_at]);
            return response()->json(['key' => $secretKey]);
        } else {
               $endTime =  $this->plusTime("+24 hours");
               $secretKey = uniqid();
                           $addUser = app('db')->insert("INSERT INTO users
                                       (email, first_name, last_name, username, sercretkey, key_at)
                                       values(?, ?, ?, ?, ?, ?)",
                               [$email, null, null, null, $secretKey,$endTime]);

               return response()->json(['key' => $secretKey]);
        }
        return response()->json(['key' => $queryCheckUser]);
    }

    public function confirm($key){
        $selectAllUsers = app('db')->select("SELECT * FROM users WHERE sercretkey = :key", ['key'=>$key]);
        if($selectAllUsers) {
            if ($selectAllUsers[0]->key_at > date("Y-m-d h:i:s")) { // check expiration
                if ($selectAllUsers[0]->registered_at == null) { //check if not registered
                    $timeNow = date("Y-m-d h:i:s");
                    $updateUser = app('db')->update("UPDATE users
                        SET sercretkey = NULL , key_at = NULL , registered_at ='$timeNow'
                        WHERE email = :email AND sercretkey = :sercretkey",
                        ['email' => $selectAllUsers[0]->email, 'sercretkey' => $selectAllUsers[0]->sercretkey]);
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

    }
}

