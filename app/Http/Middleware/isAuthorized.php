<?php

namespace App\Http\Middleware;

use Closure;

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
            $keyExists = app('db')->select("SELECT user_id, session_key FROM sessions WHERE session_key = :skey",['skey'=> $sessionKey]);
            if($keyExists){
               return $next($request);
            }
        }

        return response()->json(['Error'=>$request->header('Authorization')]);
    }
}
