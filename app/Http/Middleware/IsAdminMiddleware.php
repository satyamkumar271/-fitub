<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdminMiddleware
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
    // Check karein ki user logged in hai aur uska type 'admin' hai
    if (auth()->check() && auth()->user()->user_type == 'admin') {
        return $next($request); // Agar admin hai to aage jaane do
    }

    // Agar admin nahi hai, to use normal dashboard par bhej do ya error dikhao
    return redirect()->route('dashboard')->with('error', 'You do not have admin access.');
    // Ya aap 403 Forbidden error bhi dikha sakte hain:
    // abort(403, 'Unauthorized Access');
}
}
