<?php
namespace App\Http\Controllers;

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
          //TODO: Send UserLoginEmail
          return response()->json(['message' => 'Login email sent'], 200);
        } elseif ($user && !$user->registered_at) {
          //user exists, but email is not yet verified → expired? was not delivered? spam?
          // TODO: check if 5min passed since link was sent, and send same email again
          //Mail::to($email)->send(new UserRegistrationMail($key));
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
            $this->onLinkExpire($user->id);
            abort(response()->json(['error' => 'Key has expired'], 403));
        } else if(is_null($user->registered_at)) {
            //TODO: Move confirmRegistration to UsersManager
            $sessionKey = $this->confirmRegistration($user->id, $user->email);
            return response()->json(['message' => 'User has been registered','sessionkey' => $sessionKey], 200);
        }
        else{
          //TODO: Move confirmRegistration to UsersManager
            $sessionKey = $this->confirmLogin($user->id);
            return response()->json(['message' => 'User has been registered','sessionkey' => $sessionKey], 200);
        }
    }

    // [GENA-7]
    private function confirmRegistration($id, $email){
        //TODO:
        //$avatar = AvatarsManager::getDefault();
        //$id = UsersManager::completeRegistration()
        $avatar = null;
        $hash = md5(strtolower(trim($email)));
        $uri = 'http://www.gravatar.com/avatar/' . $hash . '?d=404&s=200';
        $headers = @get_headers($uri);
        if (preg_match("|200|", $headers[0])) {
            $avatar = $uri;
        }
        else{
            $avatar = AvatarsManager::getAvataaars();
        }
        app('db')->update("UPDATE users
                        SET registered_at = NOW(),
                            users.key_until = null,
                            users.secretkey = null,
                            users.avatar = :avatar,
                            users.auth_type = 'E'
                        WHERE users.id = :id",['id'=>$id, 'avatar'=>$avatar]);
        //TODO: send WelcomeMail
        return SessionsManager::generateSessionKey($id);
    }

    // [GENA-7]
    private function confirmLogin($id){
        app('db')->update("UPDATE users
                        SET users.key_until = null,
                            users.secretkey = null,
                            users.auth_type = 'E'
                        WHERE users.id = :id",['id'=>$id]);
        return SessionsManager::generateSessionKey($id);
    }

    // [GENA-7]
    private function onLinkExpire($id){
        app('db')->update("UPDATE users
                        SET users.key_until = null,
                            users.secretkey = null
                        WHERE users.id = :id",['id'=>$id]);
    }
}
?>
