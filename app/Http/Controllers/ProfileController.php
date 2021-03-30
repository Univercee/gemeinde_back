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
use App\Http\Controllers\EmailAuthController;
use App\Http\Controllers\TelegramAuthController;
use App\Mail\UserRegistrationMail;
use App\Managers\RecaptchaManager;
define('BOT_TOKEN', env('TG_BOT_TOKEN'));
class ProfileController extends Controller
{
  public function __construct()
  {
    $this->middleware('enforceJson', ['except' => ['setAvatar']]);
    $this->middleware('enforceJson:true', ['only' => ['setAvatar']]);
    $this->middleware('a10n');
  }


  //
  public function setAvatar(Request $request)
  {
    if (!$request->hasFile('file')) {
      abort(response()->json(['error' => 'Bad request'], 400)); 
    }
    AvatarsManager::setAvatar($request->input('user_id'), request()->file('file'));
  }


  //
  public function getAvatar(Request $request)
  {
    return response()->json(['image' => AvatarsManager::getAvatar($request->input('user_id'))], 200);
  }


  //
  public function deleteAvatar(Request $request)
  {
    AvatarsManager::deleteAvatar($request->input('user_id'));
  }


  //
  public function setPersonalDetails(Request $request){
    UsersManager::setPersonalDetails($request->input('user_id'), $request->input('firstname'), $request->input('lastname'), $request->input('language'));
  }


  //[GENA-32]
  public function getPersonalDetails(Request $request)
  {
    $personal_details = UsersManager::getPersonalDetails($request->input('user_id'));
    return response()->json(['firstname' => $personal_details->first_name, 
                            'lastname' => $personal_details->last_name, 
                            'language' => $personal_details->language], 200
                          );
  }


  //[GENA-32]
  public function getUserLocations(Request $request)
  {
    return response()->json(UsersManager::getUserLocations($request->input('user_id')), 200);
  }


  //[GENA-32]
  public function setUserLocation(Request $request)
  {
    if(!$request->input('user_location_id')){
      abort(response()->json(['error'=>'Bad request'], 400));
    }
    UsersManager::setUserLocation($request->input('user_id'), 
                                  $request->input('user_location_id'), 
                                  $request->input('location_id'), 
                                  $request->input('title'), 
                                  $request->input('street_name'), 
                                  $request->input('street_number')
                                );
  }


  //[GENA-32]
  public function addUserLocation(Request $request)
  {
    if(!$request->input('location_id')){
      abort(response()->json(['error'=>$request->all()], 400));
    }
    UsersManager::addUserLocation($request->input('user_id'), 
                                  $request->input('location_id'), 
                                  $request->input('title'), 
                                  $request->input('street_name'), 
                                  $request->input('street_number')
                                );
  }


  //[GENA-32]
  public function deleteUserLocation(Request $request)
  {
    if(!$request->input('id')){
      abort(response()->json(['error'=>'Bad request'], 400));
    }
    UsersManager::deleteUserLocation($request->input('user_id'), $request->input('id'));
  }


  //
  public function getChannels(Request $request)
  { 
    return response()->json(UsersManager::getChannels($request->input('user_id')), 200);
  }


  //
  public function addEmailChannel(Request $request){
      $email = $request->input('email');
      if(!preg_match("/^[-!#-'*+\/-9=?^-~]+(?:\.[-!#-'*+\/-9=?^-~]+)*@[-!#-'*+\/-9=?^-~]+(?:\.[-!#-'*+\/-9=?^-~]+)+$/i", $email)) {
          abort(response()->json(['error' => 'Bad Request'], 400));
      }
      $score = RecaptchaManager::getScore($request->input('token'));
      if($score < 0.5) {
          abort(response()->json(['Error'=> 'You are robot, dont event try! I am a teapot.'], 418));
      }
      $key = UsersManager::setChannelVerificationKey($request->input('user_id'), $email);
      Mail::to($email)->send(new UserRegistrationMail($key));
      return response()->json(['message' => 'Registration email sent'], 200);
  }


  //GET FUNCTION FROM EMAIL_AUTH_CONTROLLER
  public function emailChannelVerify($key){
    $user = UsersManager::getByKey($key);
    if(!$user) {
        abort(response()->json(['error' => 'Not found'], 404));
    } 
    else if(strtotime($user->verification_key_expires_at) < time()) {
        app('db')->update("UPDATE users
        SET users.verification_key_expires_at = null,
            users.verification_key = null
        WHERE users.id = :id",['id'=>$user->id]);
        abort(response()->json(['error' => 'Key has expired'], 403));
    }
    else{
      app('db')->update("UPDATE users
        SET users.verification_key_expires_at = null,
            users.verification_key = null,
            users.email = users.email_buffer,
            users.email_buffer = null
        WHERE users.id = :id",['id'=>$user->id]);
    }
    return response()->json(['message' => 'Channel has been added']);
  }


  
  //GET FUNCTION FROM TELEGRAM_AUTH_CONTROLLER
  public function tgChannelVerify(Request $request){
    $auth_data = $request->input('auth_data');
    $check_hash = $auth_data['hash'];
    unset($auth_data['hash']);

    $data_check_arr = [];
    foreach ($auth_data as $key => $value) {
      $data_check_arr[] = $key . '=' . $value;
    }
    sort($data_check_arr);
    $data_check_string = implode("\n", $data_check_arr);
    $secret_key = hash('sha256', BOT_TOKEN, true);
    $hash = hash_hmac('sha256', $data_check_string, $secret_key);

    if (strcmp($hash, $check_hash) !== 0) {
        return response()->json(['error' => 'Data is NOT from Telegram'], 400);
    }
    if ((time() - $auth_data['auth_date']) > 86400) {
        return response()->json(['error' => 'Data is outdated'], 400);
    }
    app('db')->update("UPDATE users
                      SET telegram_id = :t_id, 
                          telegram_username = :t_username
                      WHERE id = :user_id",
                      ['t_id' => $auth_data['id'], 't_username' => $auth_data['username'], 'user_id' => $request->input('user_id')]);
  }


  //
  public function deleteEmailChannel(Request $request){
    UsersManager::deleteEmailChannel($request->input('user_id'));
  }


  //
  public function deleteTgChannel(Request $request){
    UsersManager::deleteTgChannel($request->input('user_id'));
  }


  //TODO
  public function servicesFlow(Request $request, $locationId){
    $user_id = $request->input('user_id');
    $results = app('db')->select("SELECT ls.service_id, s.name_en as name, channel, frequency
              FROM location_services AS ls
              JOIN services s ON s.id = ls.service_id
              LEFT JOIN user_location_services uls ON uls.service_id = s.id
              LEFT JOIN user_locations ul ON ul.location_id = ls.location_id
              WHERE ls.location_id = :locId AND ul.user_id = :user_id", ['user_id' => $user_id, 'locId' => $locationId]);
    return response()->json(['results'=>$results]);

  }
}
