<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use Spatie\Permission\PermissionRegistrar;

class ScopePermissionsPerTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Resolve current tenant (Filament tenancy)
        $tenant = Filament::getTenant();
        $tenantId = optional($tenant)->getKey();
        $tenantKey = $tenantId ?? 'global';

        // Set Spatie team context
        app(PermissionRegistrar::class)->setPermissionsTeamId($tenantId);

        // Use a tenant-specific cache key to avoid collisions
        config(['permission.cache.key' => 'spatie.permission.cache.tenant.' . $tenantKey]);

        return $next($request);
    }
}