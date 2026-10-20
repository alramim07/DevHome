<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Section-based access control. Applied as ->middleware('section:blog').
 * Super admins always pass; other users need the section granted in their
 * per-user permissions list. Guests fall through to the auth middleware,
 * which runs first in the route groups.
 */
class EnsureSectionAccess
{
    public function handle(Request $request, Closure $next, string $section): Response
    {
        $user = $request->user();

        if ($user && ! $user->canAccess($section)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden'], 403);
            }

            return response()->view('admin.403_redirect', [
                'message' => 'Unauthorized action. Redirecting to dashboard...',
                'url' => route('dashboard'),
            ], 403);
        }

        return $next($request);
    }
}
