<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsCompany
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || $request->user()->user_type !== 'company') {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé. Cette ressource est réservée aux entreprises.',
            ], 403);
        }

        return $next($request);
    }
}
