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
  public function __construct()
  {
    $this->middleware('enforceJson', ['except' => ['setAvatar']]);
    $this->middleware('enforceJson:true', ['only' => ['setAvatar']]);
    $this->middleware('a10n');
  }

  public function setAvatar(Request $request){
    if ($request->hasFile('file')) {
      $user_id = $request->input('user_id');
      $url = Storage::disk('local')->url('app/avatars/'.$user_id.'.jpg');
      Storage::disk('local')->putFileAs('avatars',request()->file('file'), $user_id.'.jpg');
      AvatarsManager::setAvatar($user_id, $url);
      return response()->json(['auth'=>$request->header('Authorization')]);
    }
    return response()->json(['Error' => 'Image not found'],404);
  }

  public function getAvatar(Request $request){
    $user_id = $request->input('user_id');
    return response()->json(['image' => AvatarsManager::getAvatar($user_id)]);
  }

  public function deleteAvatar(Request $request){
    $user_id = $request->input('user_id');
    Storage::disk('local')->delete('app/avatars/'.$user_id.'.jpg');
    app('db')->update('UPDATE users
                        SET avatar = NULL
                        WHERE id = :user_id',
      ['user_id' => $user_id]);
  }

  public function setPersonalDetails(Request $request)
  {
    $user_id = $request->input('user_id');

    $firstname = trim($request->input('firstname'));
    $lastname = trim($request->input('lastname'));
    $language = $request->input('language');

    $firstname = ($firstname == "") ? null : $firstname;
    $lastname = ($lastname == "") ? null : $lastname;
    $language = ($language == 'en' || $language == 'de') ? $language : 'en';
    app('db')->update("UPDATE users
                        SET first_name = :firstname, last_name = :lastname, language = :language
                        WHERE id = :user_id",
      ['firstname' => $firstname, 'lastname' => $lastname, 'language' => $language, 'user_id' => $user_id]);
    return response()->json(['message' => 'Personal details has updated'], 200);
  }

  //[GENA-32]
  public function getPersonalDetails(Request $request)
  {
    $user_id = $request->input('user_id');
    $user_data = app('db')->select("SELECT first_name, last_name, language FROM users
                                        WHERE id = :user_id",
      ['user_id' => $user_id]);
    $user_data = empty($user_data) ? null : $user_data[0];
    return response()->json(['firstname' => $user_data->first_name, 'lastname' => $user_data->last_name, 'language' => $user_data->language], 200);
  }

  //[GENA-32]
  public function getUserLocations(Request $request){
    $user_id = $request->input('user_id');
    $user_locations = app('db')->select("SELECT id, location_id, title, street_name, street_number FROM user_locations
                                        WHERE user_id = :user_id",
                                        ['user_id' => $user_id]);
    return response()->json($user_locations, 200);
    }

  //[GENA-32]
  public function setUserLocation(Request $request){
    $user_id = $request->input('user_id');

    $id = $request->input('id');
    $location_id = $request->input('location_id');
    $title = trim($request->input('title'));
    $street_name = trim($request->input('street_name'));
    $street_number = trim($request->input('street_number'));
    app('db')->update("UPDATE user_locations
                      SET title = :title, location_id = :location_id, street_name = :street_name, street_number = :street_number
                      WHERE user_id = :user_id AND id = :id",
                      ['title' => $title,
                      'location_id' => $location_id,
                      'street_name' => $street_name,
                      'street_number' => $street_number,
                      'id' => $id,
                      'user_id' => $user_id]);
  }

  //[GENA-32]
  public function addUserLocation(Request $request){
    $user_id = $request->input('user_id');
    app('db')->insert("INSERT INTO user_locations(user_id, title)
                      VALUES(?, 'New location')",
                      [$user_id]);
  }

  //[GENA-32]
  public function deleteUserLocation(Request $request){
    $user_id = $request->input('user_id');
    $id = $request->input('id');
    app('db')->delete("DELETE FROM user_locations
                      WHERE user_id = :user_id AND id = :id",
                      ['id' => $id,
                      'user_id' => $user_id]);
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
  public function servicesFlow(Request $request, $locationId){

    $user_id = $request->input('user_id');

    $results = app('db') //s.name_en, uls.frequency
    ->select("SELECT ls.service_id, s.name_en as name, channel, frequency
FROM location_services AS ls
JOIN services s ON s.id = ls.service_id
LEFT JOIN user_location_services uls ON uls.service_id = s.id
LEFT JOIN user_locations ul ON ul.location_id = ls.location_id
WHERE ls.location_id = :locId AND ul.user_id = :user_id", ['user_id' => $user_id, 'locId' => $locationId]);


    return response()->json(['results'=>$results]);

  }
}
