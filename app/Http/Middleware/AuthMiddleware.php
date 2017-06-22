<?php
/**
 * Created by PhpStorm.
 * User: vdjke
 * Date: 6/7/2017
 * Time: 8:24 AM
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class AuthMiddleware
{
    /**
     * @var Guard
     */
    protected $auth;


    /**
     * AuthMiddleware constructor.
     * @param Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param $request
     * @param Closure $next
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest(config('selfy-admin.routePrefix', 'admin').'/login');
            }
        }

        return $next($request);
    }
}