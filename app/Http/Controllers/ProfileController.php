<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Managers\AvatarsManager;
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
        //$userId = AvatarsManager::setter($key); TODO <---
        Storage::disk('local')->putFileAs('avatars',request()->file('file'), $this->userId($key).'.jpg');
        return response()->json(['auth'=>$request->header('Authorization')]);
    }
      return response()->json(['Error' => 'Image not found'],404);

    }

    public function getter($avatar){
      $data = Storage::disk('local')->get('avatars/'.$avatar.'.jpg');
      var_dump($data);
    }
}
