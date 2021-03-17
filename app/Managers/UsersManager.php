<?php

namespace App\Managers;


class UsersManager{
    public static function getUserInfo($user_id){
        $userEmail = app('db')->select("SELECT email, avatar FROM users WHERE id = :id",['id'=>$user_id]);
        return $userEmail[0];
    }
}
