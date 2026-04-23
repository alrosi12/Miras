<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

/**
 * يقيّد الوصول لمسارات /admin: يجب أن يكون المستخدم مسجّلاً ومُصرَّحاً عبر Gate admin.
 */
class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! Gate::forUser($user)->allows('admin')) {
            return redirect()
                ->route('dashboard')
                ->with('error', __('You do not have access to the admin panel.'));
        }

        return $next($request);
    }
}
