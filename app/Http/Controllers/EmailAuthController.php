<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Managers\UsersManager;
use App\Managers\RecaptchaManager;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserLoginMail;
use App\Mail\UserRegistrationMail;

class EmailAuthController extends Controller
{

    public function __construct()
    {
      $this->middleware('enforceJson');
    }

    // [GENA-7]
    public function authenticate(Request $request) {
        $email = $request->input('email');
        $lang = $request->input('lang');
        if(!preg_match("/^[-!#-'*+\/-9=?^-~]+(?:\.[-!#-'*+\/-9=?^-~]+)*@[-!#-'*+\/-9=?^-~]+(?:\.[-!#-'*+\/-9=?^-~]+)+$/i", $email)) {
            abort(response()->json(['error' => 'Bad Request'], 400));
        }

        $score = RecaptchaManager::getScore($request->input('token'));
        if($score < 0.5) {
            abort(response()->json(['Error'=> __('auth.botError')], 418));
        }

        $user = UsersManager::getByEmail($email);

        if($user && $user->registered_at) {
          $key = UsersManager::setVerificationKey($email);
          Mail::to($email)->send(new UserLoginMail($key));
          return response()->json(['status' => 'login', 'message' => __('auth.verifyLinkMsg')]);
        } elseif ($user && !$user->registered_at && UsersManager::waitFiveMin($user)) {
            return response()->json(['message' => __('auth.w5min')]);
        } elseif($user && !$user->registered_at) {
          $key = UsersManager::setVerificationRegistrationKey($email);
          Mail::to($email)->send(new UserRegistrationMail($key));
          return response()->json(['status' => 'regAgain', 'message' => __('auth.verifyLinkMsg')]);
        } else {
          $key = UsersManager::add($email, $lang);
          Mail::to($email)->send(new UserRegistrationMail($key));
          return response()->json(['status' => 'reg', 'message' => __('auth.verifyLinkMsg')]);
        }
    }

    // [GENA-7]
    public function verify(Request $request, $key){
        $user = UsersManager::getByKey($key);
        $ip = $request->getClientIp();
        if(!$user) {
            abort(response()->json(['error' => __('auth.keyNotFound')], 404));
        } else if(strtotime($user->verification_key_expires_at) < time()) {
            UsersManager::onLinkExpire($user->id);
            abort(response()->json(['error' => __('auth.keyExpired')], 403));
        } else if(is_null($user->registered_at)) {
            $sessionKey = UsersManager::confirmRegistrationEmail($user->id, $user->email_pending, $ip);
            return response()->json(['status' => 'registered','message' => __('auth.gzRegMsg'),'sessionkey' => $sessionKey]);
        } else {
            $sessionKey = UsersManager::confirmLoginEmail($user->id, $ip);
            return response()->json(['status' => 'logged' ,'message' => __('auth.userLoggedIn'),'sessionkey' => $sessionKey]);
        }
    }
}
