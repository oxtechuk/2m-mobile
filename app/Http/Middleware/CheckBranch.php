<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBranch
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->role !== 'admin' && is_null($user->branch_id)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'حسابك غير مرتبط بفرع حالياً. يرجى مراجعة المدير العام.'], 403);
            }
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'حسابك غير مرتبط بفرع حالياً. يرجى مراجعة المدير العام.']);
        }

        return $next($request);
    }
}
