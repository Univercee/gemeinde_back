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
        if(!preg_match("/^[-!#-'*+\/-9=?^-~]+(?:\.[-!#-'*+\/-9=?^-~]+)*@[-!#-'*+\/-9=?^-~]+(?:\.[-!#-'*+\/-9=?^-~]+)+$/i", $email)) {
            abort(response()->json(['error' => 'Bad Request'], 400));
        }

        $score = RecaptchaManager::getScore($request->input('token'));
        if($score < 0.5) {
            abort(response()->json(['Error'=> 'You are robot, dont event try! I am a teapot.'], 418));
        }

        $user = UsersManager::getByEmail($email);

        if($user && $user->registered_at) {
          $key = UsersManager::setVerificationKey($email);
          Mail::to($email)->send(new UserLoginMail($key));
          return response()->json(['message' => 'Login email sent']);
        } elseif ($user && !$user->registered_a && UsersManager::waitFiveMin($user)) {
            return response()->json(['message' => 'Please wait at least 5 min']);
        } elseif($user && !$user->registered_a) {
          $key = UsersManager::setVerificationRegistrationKey($email);
          Mail::to($email)->send(new UserRegistrationMail($key));
          return response()->json(['message' => 'Registration email sent again']);
        } else {
          $key = UsersManager::add($email);
          Mail::to($email)->send(new UserRegistrationMail($key));
          return response()->json(['message' => 'Registration email sent']);
        }
    }

    // [GENA-7]
    public function verify($key){
        $user = UsersManager::getByKey($key);
        if(!$user) {
            abort(response()->json(['error' => 'Not found'], 404));
        } else if(strtotime($user->verification_key_expires_at) < time()) {
            UsersManager::onLinkExpire($user->id);
            abort(response()->json(['error' => 'Key has expired'], 403));
        } else if(is_null($user->registered_at)) {
            $sessionKey = UsersManager::confirmRegistrationEmail($user->id, $user->email);
            return response()->json(['message' => 'User has been registered','sessionkey' => $sessionKey]);
        } else {
            $sessionKey = UsersManager::confirmLoginEmail($user->id);
            return response()->json(['message' => 'User has been logged in','sessionkey' => $sessionKey]);
        }
    }

}
