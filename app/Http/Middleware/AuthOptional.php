<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\AuthenticationException;

class AuthOptional extends Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): mixed  $next
     * @param  string[]  $guards
     * @return mixed
     */
    public function handle($request, Closure $next,...$guards): Response
    {
        try {
            $this->authenticate($request, $guards);
        } catch (AuthenticationException $e) {
            // do nothing
        }

        return $next($request);
    }
}
