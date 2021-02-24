<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class SigninupController extends Controller
{
    // [GENA-7]
    public function store(Request $request){
        $email = $request->input('email');
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            return response()->json(['error' => 'Bad Email'], 400);
        }else{
            $queryUser = app('db')->select("SELECT * FROM users WHERE email = :email", ['email' => $email]);
            $secretKey =  md5("salt007".$email.uniqid()."0004salt");
            return response()->json(['key' => $queryUser]);
        }
    }
    


    // [GENA-7 Alex]
    public function onLinkClick($key){
        $user = SigninupController::getUserByKey($key);
        if(empty($user)){
            return response()->json(['error' => 'Not found'], 404);
        }

        if(strtotime($user->key_at) < time()){
            SigninupController::onLinkExpire($user->id);
            return response()->json(['error' => 'Link has expired'], 404);
        }else{
            if(is_null($user->registered_at)){
                SigninupController::confirmRegistration($user->id);
                return response()->json(['action' => 'Registered'], 200);
            }
            else{
                SigninupController::confirmLogin($user->id);
                return response()->json(['action' => 'Login'], 200);
            }
        }
    }
    
    // [GENA-7 Alex]
    public function getUserByKey($key){
        $user = app('db')->select("SELECT id, email, key_at, registered_at FROM users
                                WHERE users.key = :k",['k'=>$key]);
        return empty($user) ? $user : $user[0];
    }

    // [GENA-7 Alex]
    public function confirmRegistration($id){
        app('db')->update("UPDATE users
                        SET registered_at = NOW(),
                            users.key_at = null,
                            users.key = null
                        WHERE users.id = :id",['id'=>$id]);
    }

    // [GENA-7 Alex]
    public function confirmLogin($id){
        app('db')->update("UPDATE users
                        SET users.key_at = null,
                            users.key = null
                        WHERE users.id = :id",['id'=>$id]);
    }

    // [GENA-7 Alex]
    public function onLinkExpire($id){
        #to do smth if the link has expired 
    }




    // [GENA-7 Alex] [for testing]
    public function setLoginKey(Request $request){
        session_start();
        $email = $request['email'];
        $key = md5("salt007".$email.uniqid());
        $time_value = $request['time_value'];
        $time_type = $request['time_type'];
        app('db')->update("UPDATE users
                        SET users.key_at = NOW() + INTERVAL $time_value $time_type,
                            users.key = :k
                        WHERE users.email = :email", ['k'=>$key, 'email'=>$email]);
        $_SESSION['key'] = $key;
        return redirect('/api/signup/testSetKey');
    }

    //[GENA-7 Alex] [for testing]
    public function testSetKeyView(){
        session_start();
        $emails = app('db')->select("SELECT email FROM users");
        $key = isset($_SESSION['key'])?$_SESSION['key']:null;
        return view('api/testSetKey',['emails'=>$emails, 'key'=>$key]);
    }
}

