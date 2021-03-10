<?php

namespace App\Services;


class SessionsService{
    public function generateSessionKey($user_id){
        $sessionKey = bin2hex(random_bytes(32));
        app('db')->insert("INSERT INTO sessions (user_id, session_key, key_at, key_until) 
                            VALUES(?, ?, NOW(), NOW()+INTERVAL 1 DAY)",
                            [$user_id, $sessionKey]);
        return $sessionKey;
    }
}

?>