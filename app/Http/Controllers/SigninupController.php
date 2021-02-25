<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SigninupController extends Controller
{
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

