<?php

namespace App\Http\Middleware\Front;
use App\Models\MasterModel;

use Closure;
use Session;
use DB;


class AuthenticateMiddleware
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
       
        return $next($request);
    }
}
