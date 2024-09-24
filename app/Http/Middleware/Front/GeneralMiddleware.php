<?php

namespace App\Http\Middleware\Front;
use App\Common\Services\WalletService;
use App\Models\MasterModel;
use Closure;
use Sentinel;
use Session;

use App\Models\SiteSettingModel;
use App\Models\StaticPageModel;

use App;
use DB;

class GeneralMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next )
    {
        view()->share('school_admin_panel_slug',config('app.project.school_admin_panel_slug'));
        return $next($request);
    }
}
