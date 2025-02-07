<?php

namespace Laravel\Pennant\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Pennant\Feature;

class EnsureFeaturesAreActive
{
    protected static ?Closure $respondUsing = null;

    /**
     * Handle the incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$features): mixed
    {
        foreach ($features as $feature) {
            if (! Feature::active($feature)) {
                return static::$respondUsing
                    ? call_user_func(static::$respondUsing, $request, $features)
                    : abort(400);
            }
        }

        return $next($request);
    }

    /**
     * Specify a callback that should be used to generate responses for failed feature checks.
     */
    public static function whenInactive(?Closure $callback): void
    {
        static::$respondUsing = $callback;
    }
}
