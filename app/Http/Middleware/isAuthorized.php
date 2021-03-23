<?php

namespace App\Http\Middleware;

use Closure;
use App\Managers\SessionsManager;
class isAuthorized
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        
        if($request->hasHeader('Authorization')){
            $sessionKey = explode(" ", $request->header('Authorization'))[1];
            $user_id = SessionsManager::getUserIdBySessionKey($sessionKey);
            if($user_id){
                $request_array =  $request->json()->all();
                $request_array['user_id'] = $user_id;
                $request->json()->replace($request_array);
                return $next($request);
            }
        }
        return response()->json(['Error'=>"Unauthorized"], 401);
    }
}
