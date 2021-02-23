<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SigninupController extends Controller
{
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
}

