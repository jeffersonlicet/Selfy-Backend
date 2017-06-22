<?php
/**
 * Created by PhpStorm.
 * User: vdjke
 * Date: 6/7/2017
 * Time: 8:22 AM
 */

namespace App\Http\Middleware;

use Auth;
use Closure;

class AclMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     * @param $permissions
     * @return mixed
     */
    public function handle($request, Closure $next, $permissions)
    {
        $perms = explode(',', $permissions);
        $user = Auth::user();
        if (!$user->can($perms)) {
            abort(403, 'Unauthorized action.');
        }
        return $next($request);
    }
}