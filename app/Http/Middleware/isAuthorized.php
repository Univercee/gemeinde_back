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
           $sessionKey = $request->header('Authorization');

           $keyExists = app('db')
               ->select("SELECT userid, sessionstring FROM sessions WHERE sessionstring = :skey",
               ['skey'=> $sessionKey]);
           if($keyExists){
               return $next($request);
           }
        }

        return response()->json(['Error'=>'Authorize error']);
    }
}
