<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Route;

class Authenticate extends Middleware
{
    protected $user_router = "user.login";
    protected $owner_router = "owner.login";
    protected $admin_router = "admin.login";
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            if (Route::is('owner.*')) {
                return route($this->owner_router);
            } elseif (Route::is('admin.*')) {
                return route($this->admin_router);
            } else {
                return route($this->user_router);
            }
        }
    }
}
