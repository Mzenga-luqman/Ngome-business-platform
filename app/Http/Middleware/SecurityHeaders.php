<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $allowUnsafeEval = filter_var(
            env('CSP_ALLOW_UNSAFE_EVAL', config('app.debug')),
            FILTER_VALIDATE_BOOL
        );
        $allowWebSockets = filter_var(
            env('CSP_ALLOW_WEBSOCKET', config('app.debug')),
            FILTER_VALIDATE_BOOL
        );

        $scriptSrc = ["'self'", "'unsafe-inline'", 'https:'];
        if ($allowUnsafeEval) {
            $scriptSrc[] = "'unsafe-eval'";
        }

        $connectSrc = ["'self'", 'https:'];
        if ($allowWebSockets) {
            $connectSrc[] = 'wss:';
            $connectSrc[] = 'ws:';
        }

        $csp = [
            "default-src 'self'",
            "img-src 'self' data: https:",
            "style-src 'self' 'unsafe-inline' https:",
            'script-src ' . implode(' ', $scriptSrc),
            "font-src 'self' data: https:",
            'connect-src ' . implode(' ', $connectSrc),
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "form-action 'self'",
        ];

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', "camera=(), microphone=(), geolocation=()");
        $response->headers->set('Content-Security-Policy', implode('; ', $csp));

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
