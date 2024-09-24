<?php

namespace App\Http\Middleware\Admin;


use Closure;
use Sentinel;
use Session;
use Flash;

class VerifyPermission {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param $permission
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
       return $next($request);
    }
}