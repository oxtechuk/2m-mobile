<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuditLogMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Log POST/PUT/DELETE requests at HTTP level as well if authenticated
        if (Auth::check() && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            // Avoid logging log routing itself or search queries
            if (!$request->is('api/*') && !$request->is('notifications/*')) {
                try {
                    AuditLog::create([
                        'user_id' => Auth::id(),
                        'action' => strtolower($request->method()) . '_request',
                        'auditable_type' => 'Route',
                        'auditable_id' => 0,
                        'old_values' => ['url' => $request->fullUrl()],
                        'new_values' => $request->except(['password', 'password_confirmation', '_token']),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                } catch (\Exception $e) {
                    logger()->error('HTTP Audit Log failed: ' . $e->getMessage());
                }
            }
        }

        return $response;
    }
}
