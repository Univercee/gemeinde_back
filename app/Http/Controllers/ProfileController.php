<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Managers\AvatarsManager;
use App\Managers\SessionsManager;
use App\Managers\UsersManager;
class ProfileController extends Controller
{
    public function setAvatar(Request $request){
      if ($request->hasFile('file')) {
        $key = explode(" ", $request->header('Authorization'))[1];
        $userId = SessionsManager::getUserIdBySessionKey($key);
        $url = Storage::disk('local')->url('app/avatars/'.$userId.'.jpg');
        Storage::disk('local')->putFileAs('avatars',request()->file('file'), $userId.'.jpg');
        AvatarsManager::setAvatar($userId, $url);

        return response()->json(['auth'=>$request->header('Authorization')]);
    }
      return response()->json(['Error' => 'Image not found'],404);
    }

    public function getAvatar(Request $request){
      $key = explode(" ", $request->header('Authorization'))[1];
      $userId = SessionsManager::getUserIdBySessionKey($key);
      return response()->json(['image' => AvatarsManager::getAvatar($userId)]);
    }

    public function deleteAvatar(Request $request){
      $key = explode(" ", $request->header('Authorization'))[1];
      $userId = SessionsManager::getUserIdBySessionKey($key);
      Storage::disk('local')->delete('app/avatars/'.$userId.'.jpg');
      app('db')->update('UPDATE users
                        SET avatar = NULL
                        WHERE id = :user_id',
                        ['user_id' => $userId]);
    }

    public function setPersonalDetails(Request $request)
    {
        $session_key = explode(" ", $request->header('Authorization'))[1];
        $user_id = SessionsManager::getUserIdBySessionKey($session_key);
        
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

    public function getPersonalDetails(Request $request)
    {
        $session_key = explode(" ", $request->header('Authorization'))[1];
        $user_id = SessionsManager::getUserIdBySessionKey($session_key);
        $user_data = app('db')->select("SELECT first_name, last_name, language FROM users
                                        WHERE id = :user_id",
                                        ['user_id' => $user_id]);
        $user_data = empty($user_data) ? null : $user_data[0];
        return response()->json(['firstname' => $user_data->first_name, 'lastname' => $user_data->last_name, 'language' => $user_data->language], 200);
    }

    public function getUserLocations(Request $request){
      $session_key = explode(" ", $request->header('Authorization'))[1];
      $user_id = SessionsManager::getUserIdBySessionKey($session_key);
  
      $user_locations = app('db')->select("SELECT * FROM user_locations
                                          WHERE user_id = :user_id",
                                          ['user_id' => $user_id]);
      return response()->json($user_locations, 200);
    }

    //[GENA-32]
    public function setUserLocation(Request $request){
      $session_key = explode(" ", $request->header('Authorization'))[1];
      $user_id = SessionsManager::getUserIdBySessionKey($session_key);

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
    $session_key = explode(" ", $request->header('Authorization'))[1];
    $user_id = SessionsManager::getUserIdBySessionKey($session_key);

    app('db')->insert("INSERT INTO user_locations(user_id, title)
                      VALUES(?, 'New location')",
                      [$user_id]);
  }

  //[GENA-32]
  public function deleteUserLocation(Request $request){
    $session_key = explode(" ", $request->header('Authorization'))[1];
    $user_id = SessionsManager::getUserIdBySessionKey($session_key);
    $id = $request->input('id');

    app('db')->delete("DELETE FROM user_locations
                      WHERE user_id = :user_id AND id = :id",
                      ['id' => $id, 
                      'user_id' => $user_id]);
  }
}
