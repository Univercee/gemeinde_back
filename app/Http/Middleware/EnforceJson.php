<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use \Illuminate\Http\JsonResponse;

class EnforceJson
{
     /**
     * @param Request $request
     * @param \Closure $next
     * @param boolean $allowUpload
     */
    public function handle(Request $request, \Closure $next, $allowUpload = false)
    {   
        if (empty($request->all())) {
            $request->headers->set('Content-Type', 'application/json');
            return $next($request);
        }
        else if($request->isJson() || $allowUpload){
            return $next($request);
        }
        return response()->json(('Not a JSON format'), 415);
        
    }
}
