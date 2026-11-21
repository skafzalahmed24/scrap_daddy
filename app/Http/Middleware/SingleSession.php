<?php

namespace App\Http\Middleware;

use App\Services\SessionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Website pages: if the customer logged in somewhere else (another browser or the app),
 * this session is ended. Sessions that pre-date this feature simply adopt the current marker.
 */
class SingleSession
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->login_marker) {
            $mine = $request->session()->get('login_marker');

            if ($mine === null) {
                $request->session()->put('login_marker', $user->login_marker);
            } elseif ($mine !== $user->login_marker) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json(['status' => 0, 'code' => 'session_replaced', 'message' => SessionService::MESSAGE], 401);
                }

                return redirect('/customer/login?replaced=1');
            }
        }

        return $next($request);
    }
}
