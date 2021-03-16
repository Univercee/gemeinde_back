<?php
namespace App\Managers;
use Illuminate\Http\Request;
class AvatarsManager{
    public static function getAvatar($user_id){
        $avatar = app('db')->select("SELECT avatar FROM users
                                        WHERE id = :user_id",
                                        ['user_id' => $user_id]);
        $avatar = empty($avatar) ? null : $avatar[0]->avatar;
        return $avatar;
    }

    public static function setAvatar($user_id, $avatar){
        app('db')->update("UPDATE users 
                        SET avatar = :avatar
                        WHERE id = :user_id",
                        ['user_id' => $user_id, 'avatar' => $avatar]);
    }
}

?>