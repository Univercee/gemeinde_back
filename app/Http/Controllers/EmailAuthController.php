<?php
namespace App\Http\Controllers;

use App\Mail\UserLoginMail;
use Illuminate\Http\Request;

use App\Managers\SessionsManager;
use App\Managers\AvatarsManager;
use App\Managers\UsersManager;
use App\Managers\RecaptchaManager;

use Illuminate\Support\Facades\Mail;
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
          //user exists and email is verified → user wants to log in
          $key = UsersManager::setVerificationKey($email);
          Mail::to($email)->send(new UserLoginMail($key));
          return response()->json(['message' => 'Login email sent'], 200);
        } elseif ($user && !$user->registered_at) {
          //user exists, but email is not yet verified → expired? was not delivered? spam?
          // TODO: check if 5min passed since link was sent, and send same email again
//          date_default_timezone_set('Europe/Tallinn');

//          $timePlus = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." -5 minutes"));
//          $timeVer = date("Y-m-d H:i:s",strtotime(date($user->verification_key_expires_at)." -1 day"));
          if(UsersManager::waitFiveMin($user)) {
            return response()->json(['message' => 'Please wait atleast 5 min'], 200);
          }
          $key = UsersManager::setVerificationRegistrationKey($email);
          Mail::to($email)->send(new UserRegistrationMail($key));
          return response()->json(['message' => 'Registration email sent again'], 200);
        } else {
          //user does not exist → user wants to register
          $key = UsersManager::add($email);
          Mail::to($email)->send(new UserRegistrationMail($key));
          return response()->json(['message' => 'Registration email sent'], 200);
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
            return response()->json(['message' => 'User has been registered','sessionkey' => $sessionKey], 200);
        }
        else{
            $sessionKey = UsersManager::confirmLoginEmail($user->id);
            return response()->json(['message' => 'User has been registered','sessionkey' => $sessionKey], 200);
        }
    }


}
