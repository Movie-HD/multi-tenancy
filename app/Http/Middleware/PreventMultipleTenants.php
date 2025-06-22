<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventMultipleTenants
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si el usuario está autenticado
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();

        // Detectar si es una ruta de registro de tenant
        $isRegistrationRoute = $this->isRegistrationRoute($request);

        if ($isRegistrationRoute) {
            // Verificar si el usuario ya pertenece a alguna organización
            if ($user->organizacion()->exists()) {
                // Obtener la primera organización del usuario
                $firstTenant = $user->organizacion()->first();

                // Si el request es AJAX/API, devolver JSON
                if ($request->expectsJson()) {
                    return response()->json(
                        [
                            "message" => "Ya perteneces a una organización.",
                            "redirect" => "/dashboard/{$firstTenant->slug}",
                        ],
                        403
                    );
                }

                // Para requests normales, mostrar mensaje y redirigir
                session()->flash(
                    "warning",
                    "Ya perteneces a una organización. Has sido redirigido a tu dashboard."
                );
                return redirect("/dashboard/{$firstTenant->slug}");
            }
        }

        return $next($request);
    }

    /**
     * Determinar si la ruta actual es de registro de tenant
     */
    private function isRegistrationRoute(Request $request): bool
    {
        $path = $request->path();

        return $request->routeIs("filament.tenant.register") ||
            $request->routeIs("filament.dashboard.tenant.registration") ||
            str_contains($path, "register-team") ||
            preg_match("/dashboard\/[^\/]+\/register/", $path) ||
            str_contains($path, "tenant/register");
    }
}
