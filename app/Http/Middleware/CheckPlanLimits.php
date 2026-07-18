<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if ($user && !$user->is_super_admin && $user->company) {
            $company = $user->company;
            
            if ($company->plan_expires_at && $company->plan_expires_at->isPast()) {
                if (!$request->routeIs('billing.*') && !$request->routeIs('logout')) {
                    return redirect()->route('billing.index')->with('error', 'Your subscription plan has expired. Please upgrade to continue.');
                }
            }
        }
        
        return $next($request);
    }
}
