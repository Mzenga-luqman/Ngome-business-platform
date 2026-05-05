<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->is_admin || $user->hasActiveSubscription()) {
            return $next($request);
        }

        return redirect()
            ->route('subscription.plans')
            ->with('warning', 'Your subscription has expired. Please renew to continue.');
    }
}
