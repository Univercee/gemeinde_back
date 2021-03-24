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
     * @return JsonResponse
     */
    public function handle(Request $request, \Closure $next, $allowUpload = false): JsonResponse
    {
        $applicationJson = 'application/json';
        $contentType = $request->header('Content-Type');
        $validRequest = $request->header('Accept') == $applicationJson && ($contentType == $applicationJson || ($allowUpload && strpos($contentType, 'multipart/form-data') !== false));
        if (!$validRequest) {
            return response()->json(__('Not a JSON format'), 415);
        }
        return $next($request);
    }
}
