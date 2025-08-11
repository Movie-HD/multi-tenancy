<?php

namespace App\Providers;

use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\ServiceProvider;
use App\Models\Permission;
use App\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure Spatie uses our custom Permission & Role models
        app(\Spatie\Permission\PermissionRegistrar::class)
            ->setPermissionClass(Permission::class)
            ->setRoleClass(Role::class);

        // Defer tenant scoping until Filament has resolved the active tenant
        \Filament\Facades\Filament::serving(function () {
            $tenant = \Filament\Facades\Filament::getTenant();
            $tenantId = optional($tenant)->getKey();
            $tenantKey = $tenantId ?? 'global';

            // 1) Scope permissions to current tenant (teams feature)
            $registrar = app(\Spatie\Permission\PermissionRegistrar::class);
            $registrar->setPermissionsTeamId($tenantId);

            // 2) Use a per-tenant cache key so caches don't collide across tenants
            config(['permission.cache.key' => 'spatie.permission.cache.tenant.' . $tenantKey]);

            // 3) Ensure we don't reuse a previous tenant's cached data
            $registrar->forgetCachedPermissions();
        });
    }
}
