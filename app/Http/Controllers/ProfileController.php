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

    public function setProfileHead(Request $request)
    {
        $session_key = getallheaders()['Authorization'] ?? null;
        $user_id = SessionsManager::getUserIdBySessionKey($session_key);
        if(is_null($user_id)){
            return response()->json(['error' => 'Not allowed'], 401);
        }
        $firstname = str_replace(" ", "", trim($request->input('firstname')));
        $lastname = str_replace(" ", "", trim($request->input('lastname')));
        $language = str_replace(" ", "", trim($request->input('language')));
        $firstname = ($firstname == "") ? null : $firstname;
        $lastname = ($lastname == "") ? null : $lastname;
        $language = ($language == 'EN' || $language == 'DE') ? $language : 'EN';
        app('db')->update("UPDATE users 
                        SET first_name = :firstname, last_name = :lastname, language = :language
                        WHERE id = :user_id",
                        ['firstname' => $firstname, 'lastname' => $lastname, 'language' => $language, 'user_id' => $user_id]);
        return response()->json(['message' => 'Firstname has updated'], 200);
    }

    public function getProfileHead()
    {
        $session_key = getallheaders()['Authorization'] ?? null;
        $user_id = SessionsManager::getUserIdBySessionKey($session_key);
        if(is_null($user_id)){
            return response()->json(['error' => 'Not allowed'], 401);
        } 
        $user_data = app('db')->select("SELECT first_name, last_name, language FROM users
                                        WHERE id = :user_id",
                                        ['user_id' => $user_id]);
        $user_data = empty($user_data) ? null : $user_data[0];
        return response()->json(['firstname' => $user_data->first_name, 'lastname' => $user_data->last_name, 'language' => $user_data->language], 200);
    }
}
