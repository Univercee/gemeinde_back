<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;

class EmailAuthController extends Controller{

    // [GENA-7]
    public function identification(Request $request){
        $email = $request->input('email');
        if(!preg_match("/^[-!#-'*+\/-9=?^-~]+(?:\.[-!#-'*+\/-9=?^-~]+)*@[-!#-'*+\/-9=?^-~]+(?:\.[-!#-'*+\/-9=?^-~]+)+$/i", $email)) {
            return response()->json(['error' => 'Bad Request'], 400);
        }
        $score = $this->getRecaptcha($request->input('token'));
        if($score < 0.5) {
            return response()->json(['Error'=> 'You are robot, dont event try!!!!'], 400);
        }
        $user = $this->getUserByEmail($email);
        if(empty($user)){
            $send = $this->addUser($email);
            Mail::to($email)->send(new WelcomeMail($send));
            return response()->json(['message' => 'Email sent'], 200);
        }
        if(is_null($user->registered_at)){
            $send = $this->generateRegistrationKey($email);
            Mail::to($email)->send(new WelcomeMail($send));
            return response()->json(['message' => 'Email sent'], 200);
        }
        else {
            $send = $this->generateLoginKey($email);
            Mail::to($email)->send(new WelcomeMail($send));
            return response()->json(['message' => 'Email sent'], 200);
        }
    }

    // [GENA-7]
    public function authentication($key){
        $user = $this->getUserByKey($key);
        if(empty($user)){
            return response()->json(['error' => 'Not found'], 404);
        }
        if(strtotime($user->key_until) < time()){
            $this->onLinkExpire($user->id);
            return response()->json(['error' => 'Key has expired'], 403);
        }
        if(is_null($user->registered_at)){
            $this->confirmRegistration($user->id);
            $sessionKey = uniqid();
            app('db')->insert("INSERT INTO sessions(userid, sessionstring)
                            VALUES(?, '$sessionKey')", [$user->id]);
            return response()->json(['message' => 'User has been registered','sessionkey' => $sessionKey]);
        }
        else{
            $this->confirmLogin($user->id);
            $sessionKey = uniqid();
            app('db')->insert("INSERT INTO sessions(userid, sessionstring)
                            VALUES(?, '$sessionKey')", [$user->id]);
            return response()->json(['message' => 'User authorized','sessionkey' => $sessionKey]);
        }
    }

    // [GENA-7]
    private function generateLoginKey($email){
        $secretKey = uniqid();
        app('db')->update("UPDATE users
                        SET secretkey = :secretKey , key_until = NOW() + INTERVAL 5 MINUTE
                        WHERE email = :email",
                        ['email'=>$email, 'secretKey'=>$secretKey]);
        return ['key'=>$secretKey];
    }

    // [GENA-7]
    private function generateRegistrationKey($email){
        $secretKey = uniqid();
        app('db')->update("UPDATE users
                        SET secretkey = :secretKey , key_until = NOW() + INTERVAL 24 HOUR
                        WHERE email = :email",
                        ['email'=>$email, 'secretKey'=>$secretKey]);
        return ['key'=>$secretKey];
    }

    // [GENA-7]
    private function addUser($email){
        $secretKey = uniqid();
        app('db')->insert("INSERT INTO users(email, secretkey, key_until)
                            VALUES(?, ?, NOW() + INTERVAL 24 HOUR)",
                            [$email, $secretKey]);
        return ['key'=>$secretKey];
    }

    // [GENA-7]
    private function confirmRegistration($id){
        app('db')->update("UPDATE users
                        SET registered_at = NOW(),
                            users.key_until = null,
                            users.secretkey = null,
                            users.auth_type = 'E'
                        WHERE users.id = :id",['id'=>$id]);
    }

    // [GENA-7]
    private function confirmLogin($id){
        app('db')->update("UPDATE users
                        SET users.key_until = null,
                            users.secretkey = null,
                            users.auth_type = 'E'
                        WHERE users.id = :id",['id'=>$id]);
    }

    // [GENA-7]
    private function onLinkExpire($id){
        app('db')->update("UPDATE users
                        SET users.key_until = null,
                            users.secretkey = null
                        WHERE users.id = :id",['id'=>$id]);
    }

    // [GENA-7]
    private function getUserByEmail($email){
        $user = app('db')->select("SELECT id, email, key_until, registered_at FROM users
                                WHERE users.email = :email",['email'=>$email]);
        return empty($user) ? $user : $user[0];
    }

    // [GENA-7]
    private function getUserByKey($key){
        $user = app('db')->select("SELECT id, email, key_until, registered_at FROM users
                                WHERE users.secretkey = :k",['k'=>$key]);
        return empty($user) ? $user : $user[0];
    }

    // [GENA-7]
    private function getRecaptcha($token){
        $response = Http::asForm()
            ->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => env('GOOGLE_RECAPTCHA_SECRET_KEY'),
                'response' => $token,
            ])->json();
        return (float)json_encode(floatval($response['score']));
    }
}
?>
