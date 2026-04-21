<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileIsComplete
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return $next($request);
        }

        if ($request->routeIs('profile.complete.show', 'profile.complete.update')) {
            return $next($request);
        }

        if (! $user->hasCompletedProfile()) {
            return redirect()->route('profile.complete.show');
        }

        return $next($request);
    }
}
