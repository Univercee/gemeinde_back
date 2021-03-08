<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    //
    public function getUserBySessionKey(Request $request){
        $sessionKey = $request->input('sessionKey');
        $user = app('db')->select("SELECT *
                                FROM users
                                JOIN sessions ON users.id = sessions.user_id
                                WHERE session_key = :sessionKey",
                                ['sessionKey'=>$sessionKey]);
        return response()->json($user);
    }
}
