<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
        ],

        'api' => [
            'throttle:60,1',
        ],
        'admin'=>[
            \App\Http\Middleware\Admin\AuthenticateMiddleware::class,
            \App\Http\Middleware\Admin\GeneralMiddleware::class,
        ],
        'schooladmin'=>[
            \App\Http\Middleware\SchoolAdmin\AuthenticateMiddleware::class,
            \App\Http\Middleware\SchoolAdmin\GeneralMiddleware::class,
        ],
        'front'=>[
                 \App\Http\Middleware\Front\GeneralMiddleware::class,
        ],
        'module_permission'=>[
          \App\Http\Middleware\Admin\VerifyPermission::class
        ],
        'professor'=>[
             \App\Http\Middleware\Professor\AuthenticateMiddleware::class,
            \App\Http\Middleware\Professor\GeneralMiddleware::class,
        ],
        'student'=>[
             \App\Http\Middleware\Student\AuthenticateMiddleware::class,
            \App\Http\Middleware\Student\GeneralMiddleware::class,
        ],
        'parent'=>[
             \App\Http\Middleware\Parent\AuthenticateMiddleware::class,
            \App\Http\Middleware\Parent\GeneralMiddleware::class,
        ]
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'module_permission'=>\App\Http\Middleware\Admin\VerifyPermission::class,        
    ];
}
