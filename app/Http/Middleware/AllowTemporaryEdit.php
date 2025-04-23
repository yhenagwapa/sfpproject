<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class AllowTemporaryEdit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $currentMonth = Carbon::now()->format('F');
        if (($request->user()?->hasRole('child development worker') || $request->user()?->hasRole('encoder')) && $currentMonth === 'June') {
            session(['temp_can_edit' => true]);
        }

        return $next($request);
    }
}
