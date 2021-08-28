<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Managers\AvatarsManager;
use App\Managers\UsersManager;
use App\Mail\UserRegistrationMail;
use DB;
define('BOT_TOKEN', env('TELEGRAM_BOT_TOKEN'));
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
    if(AvatarsManager::setAvatar($request->input('user_id'), request()->file('file'))){
      return response()->json(['message'=>'Avatar updated'], 200);
    };
    abort(response()->json(['message'=>'Couldn\'t update avatar'], 422));

  }


  //
  public function getAvatar(Request $request)
  {
    return response()->json(['image' => AvatarsManager::getAvatar($request->input('user_id'))], 200);
  }


  //
  public function deleteAvatar(Request $request)
  {
    if(AvatarsManager::deleteAvatar($request->input('user_id'))){
      return response()->json(['message'=>'Avatar deleted'], 200);
    };
    abort(response()->json(['message'=>'Couldn\'t delete avatar'], 422));
  }


  //
  public function setPersonalDetails(Request $request){
    if(UsersManager::setPersonalDetails($request->input('user_id'), $request->input('firstname'), $request->input('lastname'), $request->input('language'))){
      return response()->json(['message'=>'Personal details updated'], 200);
    };
    abort(response()->json(['message'=>'Couldn\'t update personal details'], 422));
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
    if(!$request->input('id') || !$request->input('location_id')){
      abort(response()->json(['message'=>'Location not specified'], 400));
    }
    $services_updated = UsersManager::setUserServices($request->input('services'), $request->input('id'));
    $location_updated = UsersManager::setUserLocation($request->input('user_id'),
                                                      $request->input('id'),
                                                      $request->input('location_id'),
                                                      $request->input('title'),
                                                      $request->input('street_name'),
                                                      $request->input('street_number'),
                                                      $request->input('services')                                   
                                                    );
    if($location_updated || $services_updated){
      return response()->json(['message'=>'Location updated'], 200);
    };
    abort(response()->json(['message'=>'Couldn\'t update location'], 422));
  }


  //[GENA-32]
  public function addUserLocation(Request $request)
  {
    if(!$request->input('location_id')){
      abort(response()->json(['message'=>'Location not specified'], 400));
    }
    $id = UsersManager::addUserLocation($request->input('user_id'),
      $request->input('location_id'),
      $request->input('title'),
      $request->input('street_name'),
      $request->input('street_number'),
      $request->input('services')
    );
    if($id){
      return response()->json(['message'=>'Location added', 'id'=>$id], 200);
    };

    abort(response()->json(['message'=>'Couldn\'t add location'], 422));
  }


  //[GENA-32]
  public function deleteUserLocation(Request $request)
  {
    if(!$request->input('id')){
      abort(response()->json(['message'=>'Deleted location not chosen'], 400));
    }
    if(UsersManager::deleteUserLocation($request->input('user_id'), $request->input('id'))){
      return response()->json(['message'=>'Location deleted'], 200);
    };
    abort(response()->json(['message'=>'Couldn\'t delete location'], 422));
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
      $key = UsersManager::setChannelVerificationKey($request->input('user_id'), $email);
      Mail::to($email)->send(new UserRegistrationMail($key));
      return response()->json(['message' => __('auth.mailSend')], 200);
      return response()->json(['message'=> __('auth.mailVerified')], 200);
  }


  //GET FUNCTION FROM EMAIL_AUTH_CONTROLLER
  public function emailChannelVerify($key){
    $user = UsersManager::getByKey($key);
    if(!$user) {
        abort(response()->json(['error' => __('auth.notFound')], 404));
    }
    else if(strtotime($user->verification_key_expires_at) < time()) {
        app('db')->update("UPDATE users
        SET users.verification_key_expires_at = null,
            users.verification_key = null
        WHERE users.id = :id",['id'=>$user->id]);
        abort(response()->json(['error' => __('auth.keyExpired')], 403));
    }
    else{
      app('db')->update("UPDATE users
        SET users.verification_key_expires_at = null,
            users.verification_key = null,
            users.email = users.email_pending,
            users.email_pending = null
        WHERE users.id = :id",['id'=>$user->id]);
    }
    return response()->json(['message' => __('auth.channelAdded')]);
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
        return response()->json(['error' => __('auth.notTgData')], 400);
    }
    if ((time() - $auth_data['auth_date']) > 86400) {
        return response()->json(['error' => __('auth.outDateData')], 400);
    }
    app('db')->update("UPDATE users
                      SET telegram_id = :t_id,
                          telegram_username = :t_username
                      WHERE id = :user_id",
                      ['t_id' => $auth_data['id'], 't_username' => $auth_data['username'], 'user_id' => $request->input('user_id')]);
  }


  //
  public function deleteEmailChannel(Request $request){
    if(UsersManager::deleteEmailChannel($request->input('user_id'))){
      return response()->json(['message'=>'Email channel deleted'], 200);
    };

    abort(response()->json(['message'=>'Couldn\'t delete email channel'], 422));

  }


  //
  public function deleteTgChannel(Request $request){
    if(UsersManager::deleteTgChannel($request->input('user_id'))){
      return response()->json(['message'=>'Telegram channel deleted'], 200);
    };
    abort(response()->json(['message'=>'Couldn\'t delete telegram channel'], 422));
  }


  //TODO
  public function servicesFlow(Request $request, $locationId, $user_location_id){
    $results = UsersManager::getUserServices($request->input('user_id'), $locationId, $user_location_id);
    return response()->json(['results'=>$results]);
  }
}
