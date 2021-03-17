<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Managers\AvatarsManager;
use App\Managers\SessionsManager;
use App\Managers\UsersManager;
class ProfileController extends Controller
{
  public function userId($key){
    $user = app('db')->select("SELECT user_id FROM sessions WHERE session_key = :key", ["key"=> $key]);
    return $user[0]->user_id;
  }
 public function userInfo(Request $request){
        return response()->json(['Message'=>'Go to profile']);
    }
    public function setAvatar(Request $request){
      if ($request->hasFile('file')) {
        $key = explode(" ", $request->header('Authorization'))[1];
        $userId = SessionsManager::getUserIdBySessionKey($key);
        $url = Storage::disk('local')->url('app/avatars/'.$this->userId($key).'.jpg');
        Storage::disk('local')->putFileAs('avatars',request()->file('file'), $userId.'.jpg');
        AvatarsManager::setAvatar($userId, $url);

        return response()->json(['auth'=>$request->header('Authorization')]);
    }
      return response()->json(['Error' => 'Image not found'],404);

    }

    public function getAvatar(Request $request){
      $key = explode(" ", $request->header('Authorization'))[1];
      $userId = SessionsManager::getUserIdBySessionKey($key);
      $userAvatar = UsersManager::getUserInfo($userId);
      $url = Storage::disk('local')->url('app/avatars/'.$userAvatar->avatar.'.jpg');
      return response()->json(['formdisk' => $url, 'fromdb'=> AvatarsManager::getAvatar($userId)]);
    }
}
