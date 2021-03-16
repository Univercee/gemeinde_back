<?php

namespace App\Services;


class SessionsManager{
    public static function generateSessionKey($user_id){
        $sessionKey = bin2hex(random_bytes(32));
        app('db')->insert("INSERT INTO sessions (user_id, session_key, key_at, key_until) 
                            VALUES(?, ?, NOW(), NOW()+INTERVAL 1 DAY)",
                            [$user_id, $sessionKey]);
        return $sessionKey;
    }

    public static function getUserIdBySessionKey($session_key){
        $user = app('db')->select("SELECT user_id FROM sessions
                                    WHERE session_key = :session_key AND key_until > NOW()",
                                    ['session_key' => $session_key]);
        $user_id = empty($user) ? null : $user[0]->user_id;
        return $user_id;
    }
}

?>