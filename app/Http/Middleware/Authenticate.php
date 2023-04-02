<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    // protected function redirectTo(Request $request): ?string
    // {
    //     return $request->expectsJson() ? null : route('looog');
    // }

    // when the user is not authenticated, he will not be redirected to the login page, but receives a 401 error
    protected function redirectTo(Request $request)
    {
        return Response::json([
            'message' => 'Unauthorized'
        ], 401);
    }

}
