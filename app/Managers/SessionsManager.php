<?php
namespace App\Managers;

class SessionsManager
{
    public static function generateSessionKey($user_id, $ip_address) {
        $sessionKey = bin2hex(random_bytes(32));
        app('db')->insert("INSERT INTO user_sessions (user_id, session_key, issued_at, expires_at, ip_address)
                            VALUES(?, ?, NOW(), NOW()+INTERVAL 1 DAY, ?)",
                            [$user_id, $sessionKey, $ip_address]);
        return $sessionKey;
    }

    public static function getUserIdBySessionKey($session_key) {
        $user = app('db')->select("SELECT user_id, expires_at FROM user_sessions
                                    WHERE session_key = :session_key AND expires_at > NOW()",
                                    ['session_key' => $session_key]);
        return empty($user) ? null : $user[0]->user_id;
    }
}
