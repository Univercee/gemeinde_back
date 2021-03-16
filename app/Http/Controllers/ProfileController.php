<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Managers\AvatarsManager;
use App\Managers\SessionsManager;
class ProfileController extends Controller
{
  public function userId($key){
    $user = app('db')->select("SELECT user_id FROM sessions WHERE session_key = :key", ["key"=> $key]);
    return $user[0]->user_id;
  }
 public function userInfo(Request $request){
        return response()->json(['Message'=>'Go to profile']);
    }
    public function setter(Request $request){
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

    public function getter(Request $request, $avatar){
      $key = '26f26a10d759475837bfb3cfb9467ec611725b8001f96778904a597c038f07b4';
      $userId = SessionsManager::getUserIdBySessionKey($key);
      $url = Storage::disk('local')->url('app/avatars/'.$avatar.'.jpg');
      return response()->json(['formdisk' => $url, 'fromdb'=> AvatarsManager::getAvatar($userId)]);
    }
}
