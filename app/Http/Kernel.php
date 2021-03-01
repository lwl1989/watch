<?php

namespace App\Http;

use App\Http\Middleware\AuthAdmin;
use App\Http\Middleware\FormatResponse;
use App\Http\Middleware\JsonInput;
use Illuminate\Database\DatabaseManager;
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
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
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
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'json_input',
            //'throttle:600,1',
            'bindings'
        ],
        'format' => [
            FormatResponse::class
        ],
        'auth_admin' => [
            AuthAdmin::class
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
        'json_input'	=> JsonInput::class,
        'auth'			=> \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic'	=> \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings'		=> \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can'			=> \Illuminate\Auth\Middleware\Authorize::class,
        'guest'			=> \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle'		=> \Illuminate\Routing\Middleware\ThrottleRequests::class
    ];


    /**
     * Call the terminate method on any terminable middleware.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    public function terminate($request, $response)
    {
        $this->terminateMiddleware($request, $response);

        $this->app->terminate();

        $dba = $this->app->get("db");
        if($dba instanceof DatabaseManager) {
            $dba->disconnect();
        }
        if($this->app['redis']) {
            unset($this->app['redis']);
        }
    }
}
