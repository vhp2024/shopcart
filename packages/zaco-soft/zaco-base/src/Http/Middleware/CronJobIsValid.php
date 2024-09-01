<?php

namespace ZacoSoft\ZacoBase\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CronJobIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->input('token') !== '3d33eab3-bde7-4dfc-8930-db575f8fb828') {
            return redirect('/');
        }

        return $next($request);
    }
}
