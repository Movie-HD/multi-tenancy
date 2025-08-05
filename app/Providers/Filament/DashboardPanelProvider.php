<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\TenantRegister;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Models\Organizacion; # <-- Add Model Tenant
use App\Filament\Pages\Tenancy\RegisterTeam;
use App\Filament\Pages\Tenancy\EditTeamProfile;
use App\Filament\Pages\Auth\EditProfile;
use Filament\Navigation\MenuItem;
use App\Http\Middleware\PreventMultipleTenants;

class DashboardPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('dashboard')
            ->path('dashboard')
            ->registration(TenantRegister::class)
            ->login()
            ->tenant(Organizacion::class, slugAttribute: 'slug')
            ->tenantProfile(EditTeamProfile::class)
            ->tenantMiddleware([
                \BezhanSalleh\FilamentShield\Middleware\SyncShieldTenant::class,
            ], isPersistent: true)
            ->profile(EditProfile::class)
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            ])
            ->tenantRegistration(RegisterTeam::class)
            ->tenantMenuItems([
                'register' => MenuItem::make()->hidden(true),
                'profile' => MenuItem::make()->hidden(fn () => ! (
                    \Filament\Facades\Filament::getTenant()?->users()
                        ->wherePivot('is_owner', true)
                        ->where('users.id', auth()->id())
                        ->exists()
                )),
            ])
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                PreventMultipleTenants::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
