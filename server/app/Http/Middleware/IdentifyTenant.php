<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // Check for a tenant identifier in the request headers
        $tenantSlug = $request->header('X-Tenant-Slug');

        if ($tenantSlug) {
            // Find the organization by slug
            $organization = Organization::where('slug', $tenantSlug)->first();

            // If not found, abort (security measure)
            if (!$organization) {
                abort(404, 'Tenant not found.');
            }

            // Bind the organization to the service container for global access
            app()->instance('organization', $organization);

        }

        return $next($request);
    }
}
