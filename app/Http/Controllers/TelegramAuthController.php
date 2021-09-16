<?php
namespace App\Http\Controllers;

use App\Managers\UsersManager;
use Illuminate\Http\Request;
use App\Managers\SessionsManager;
use App\Managers\TelegramManager;
define('BOT_TOKEN', env('TELEGRAM_BOT_TOKEN'));

class TelegramAuthController extends Controller{

    public function __construct()
    {
      $this->middleware('enforceJson');
    }

    // [GENA-9]
    // code from https://gist.github.com/anonymous/6516521b1fb3b464534fbc30ea3573c2
    public function authentication(Request $request) {
        $auth_data = $request->input('auth_data');
        $ip = $request->ip();

        $check_hash = $auth_data['hash'];
        unset($auth_data['hash']);
        if (!TelegramManager::checkHash($auth_data, $check_hash)) {
            return abort(response()->json(['error' => 'Data is NOT from Telegram'], 400));
        }
        if ((time() - $auth_data['auth_date']) > 86400) {
            return response()->json(['error' => 'Data is outdated'], 400);
        }
        $user = UsersManager::getUserByTelegramId($auth_data['id']);

        if(empty($user)){
            $sessionKey = UsersManager::confirmRegistration($auth_data, $ip);
            return response()->json(['message' => 'User has been registered','sessionkey' => $sessionKey], 200);
        }
        else{
            //$sessionKey = UsersManager::confirmLogin($auth_data['id'], $user->id);
            return response()->json(['message' => 'User authorized','sessionkey' => SessionsManager::generateSessionKey($user->id, $ip)], 200);
        }
    }
}

