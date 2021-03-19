<?php


namespace App\Http\Controllers;


use App\Mail\WelcomeMail;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Managers\AvatarsManager;
use App\Managers\SessionsManager;
use App\Managers\UsersManager;
use App\Mail\ChannelMail;
define('BOT_TOKEN', env('TG_BOT_TOKEN'));
class ProfileController extends Controller
{

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
      return response()->json(['image'=> AvatarsManager::getAvatar($userId)]);
    }
    public function getUserInfo(Request $request){
      $key = explode(" ", $request->header('Authorization'))[1];
      $userId = SessionsManager::getUserIdBySessionKey($key);
      $userEmail = UsersManager::getUserInfo($userId);
      return response()->json(['email'=>$userEmail->email]);
    }
    public function getChannels(Request $request){
      $key = explode(" ", $request->header('Authorization'))[1];
      $userId = SessionsManager::getUserIdBySessionKey($key);
      $userData = UsersManager::getUserInfo($userId);

      return response()->json([$userData]);
    }

  public function confirmChannel(Request $request){
    $key = explode(" ", $request->header('Authorization'))[1];
    $userId = SessionsManager::getUserIdBySessionKey($key);
    $userInfo = UsersManager::getUserInfo($userId);

    $auth_data = $request['auth_data'];
    $check_hash = $auth_data['hash'];
    unset($auth_data['hash']);

    $hash = UsersManager::getAuthHash($auth_data);
    if (strcmp($hash, $check_hash) !== 0) {

      return response()->json(['error' => 'Data is NOT from Telegram'], 400);
    }

    app('db')->update("UPDATE users
                            SET users.telegram_id = :telegram_id, username = :username
                            WHERE users.email = :email",
      ['telegram_id'=>$auth_data['id'], 'email'=>$userInfo->email, 'username'=>$auth_data['username']]);
      return response()->json(['message' => 'User channel authorized']);
  }
  public function confirmEmailChannel(Request $request){
    $key = explode(" ", $request->header('Authorization'))[1];
    $userId = SessionsManager::getUserIdBySessionKey($key);
//    return response()->json(['message' => $userId]);

    app('db')->update("UPDATE users
                        SET email = 'daig@mail.ru'
                        WHERE id = :id",['id'=>$userId]);
    return response()->json(['message' => 'User channel authorized']);

  }
  ///Email identification for channel
  public function identification(Request $request){
    $key = explode(" ", $request->header('Authorization'))[1];
    $userId = SessionsManager::getUserIdBySessionKey($key);
    $email = $request->input('email');
    if(!preg_match("/^[-!#-'*+\/-9=?^-~]+(?:\.[-!#-'*+\/-9=?^-~]+)*@[-!#-'*+\/-9=?^-~]+(?:\.[-!#-'*+\/-9=?^-~]+)+$/i", $email)) {
      return response()->json(['error' => 'Bad Request'], 400);
    }
    $score = UsersManager::getRecaptcha(($request->input('token')));
    if($score < 0.5) {
      return response()->json(['Error'=> 'You are robot, dont event try!!!!'], 400);
    }
      $send = UsersManager::addUser($email, $userId);
      Mail::to($email)->send(new ChannelMail($send));
      return response()->json(['message' => 'Email sent'], 200);
  }

  //verify channel connection by verify link
  public function authentication(Request $request,$key){
    $user = UsersManager::getUserByKey($key);
    $key = explode(" ", $request->header('Authorization'))[1];
    $userId = SessionsManager::getUserIdBySessionKey($key);
    if(empty($user)){
      return response()->json(['error' => 'Not found'], 403);
    }
    if(strtotime($user->key_until) < time()){
      $this->onLinkExpire($userId->id);
      return response()->json(['error' => 'Key has expired'], 403);
    }
    app('db')->update("UPDATE users
                        SET users.key_until = null,
                            users.secretkey = null,
                            users.auth_type = 'E'
                        WHERE users.id = :id",['id'=>$userId]);
      return response()->json(['message' => 'User has been registered','useremail' => UsersManager::getUserInfo($user->id)->email]);

  }

}
