<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
    


    // [GENA-7]
    public function onLinkClick($key){
        $user = SigninupController::getUserByKey($key);
        if(empty($user)){
            return response()->json(['error' => 'Not found'], 404);
        }else{
            $delta_time = time() - strtotime($user->key_at);
            if(is_null($user->registered_at)){
                if($delta_time < 86400){
                    SigninupController::confirmRegistration($user->id);
                    echo($user->email." registration");
                }else{
                    echo($user->email." link expire");
                }
            }else{
                if($delta_time < 600){
                    SigninupController::confirmLogin($user->id);
                    echo($user->email. " login");
                }else{
                    echo($user->email." link expire");
                }
            }
        }
    }


    // [GENA-7]
    public function getUserByKey($key){
        $user = app('db')->select("SELECT * FROM users                  #id, key_at, registered_at
                                WHERE users.key = :k",['k'=>$key]);
        return empty($user) ? $user : $user[0];
    }


    // [GENA-7]
    public function confirmRegistration($id){
        app('db')->update("UPDATE users
                        SET registered_at = NOW(),
                            users.key_at = null,
                            users.key = null
                        WHERE users.id = :id",['id'=>$id]);
    }


    // [GENA-7]
    public function confirmLogin($id){
        app('db')->update("UPDATE users
                        SET users.key_at = null,
                            users.key = null
                        WHERE users.id = :id",['id'=>$id]);
    }


    // [GENA-7]
    public function setLoginKey(Request $request){
        $email = $request['email'];
        var_dump($email);
        $key = $request['key'];
        var_dump($key);
        app('db')->update("UPDATE users
                        SET users.key_at = NOW(),
                            users.key = :k
                        WHERE users.email = :email", ['k'=>$key, 'email'=>$email]);
    }

    //[GENA-7]
    public function testSetKeyView(){
        $emails = app('db')->select("SELECT email FROM users");
        return view('api/testSetKey',['emails'=>$emails]);
    }
}

