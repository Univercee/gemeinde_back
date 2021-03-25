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

        
        $sessionKey = $request->bearerToken();
        if($sessionKey){
            $user_id = SessionsManager::getUserIdBySessionKey($sessionKey);
            if($user_id){
                $request->merge(['user_id' => $user_id]);
                return $next($request);
            }
        }
        return response()->json(['Error'=>"Unauthorized"], 401);
    }
}
