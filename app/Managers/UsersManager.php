<?php

namespace App\Managers;


class UsersManager{
    public static function getUserEmail($user_id){
        $userEmail = app('db')->select("SELECT email FROM users WHERE id = :id",['id'=>$user_id]);
        return $userEmail[0]->email;
    }
}
