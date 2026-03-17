<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $plainToken = $request->bearerToken();

        if (! $plainToken) {
            return response()->json(['message' => 'Authentication token is required.'], 401);
        }

        $tokenHash = hash('sha256', $plainToken);
        $user = User::query()->where('api_token', $tokenHash)->first();

        if (! $user) {
            return response()->json(['message' => 'Invalid authentication token.'], 401);
        }

        Auth::setUser($user);
        $request->setUserResolver(static fn() => $user);

        return $next($request);
    }
}
