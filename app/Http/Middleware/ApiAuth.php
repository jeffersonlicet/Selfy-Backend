<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class ApiAuth
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
        try {
            if ($user = JWTAuth::parseToken()->authenticate()) {
                return $next($request);
            } else {
                return response()->json(['status' => FALSE, 'report' => 'user_not_found'], 404);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['status' => FALSE, 'report' => 'token_expired'], $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return response()->json(['status' => FALSE, 'report' => 'token_invalid'], $e->getStatusCode());
        } catch (JWTException $e) {
            return response()->json(['status' => FALSE, 'report' =>  'token_absent'], $e->getStatusCode());
        }
    }
}
