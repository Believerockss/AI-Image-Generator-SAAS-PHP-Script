<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsSubscribed
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
        if (!auth()->user()->isSubscribed() || !auth()->user()->subscription->isActive()) {
            toastr()->info(lang('You are not subscribed or your subscribtion is expired, please subscribe or upgrade your subscription', 'account'));
            return redirect()->route('user.settings.subscription');
        }
        return $next($request);
    }
}
