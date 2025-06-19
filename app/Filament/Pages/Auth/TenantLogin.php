<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use App\Models\Organizacion;
use Filament\Notifications\Notification;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Support\Facades\DB;

class TenantLogin extends Login
{
    public function authenticate(): ?LoginResponse
    {
        // Obtener el host actual
        $host = request()->getHost();
        $isMainDomain = $host === 'multi-tenancy.test';

        // Si estamos en un subdominio, verificar que el usuario pertenezca a este tenant
        if (!$isMainDomain) {
            $parts = explode('.', $host);
            $slug = $parts[0] ?? null;
            
            if ($slug) {
                $tenant = Organizacion::where('slug', $slug)->first();
                
                if ($tenant) {
                    // Intentamos autenticar pero sin iniciar sesión aún
                    $credentials = $this->getCredentialsFromFormData($this->data);
                    
                    if (Auth::validate($credentials)) {
                        // Obtenemos el usuario sin iniciar sesión
                        $user = Auth::getProvider()->retrieveByCredentials($credentials);
                        
                        // Verificar si el usuario pertenece a este tenant específico
                        $belongsToTenant = DB::table('organizacion_user')
                            ->where('user_id', $user->id)
                            ->where('organizacion_id', $tenant->id)
                            ->exists();
                        
                        if (!$belongsToTenant) {
                            // El usuario no pertenece a este tenant
                            Notification::make()
                                ->title('Acceso denegado')
                                ->body("Las credenciales proporcionadas no son válidas para esta organización.")
                                ->danger()
                                ->persistent()
                                ->send();
                            
                            return null;
                        }
                    }
                }
            }
        }
        
        // Si estamos en el dominio principal, verificamos si el usuario pertenece a algún tenant
        if ($isMainDomain) {
            // Intentamos autenticar pero sin iniciar sesión aún
            $credentials = $this->getCredentialsFromFormData($this->data);
            
            if (Auth::validate($credentials)) {
                // Obtenemos el usuario sin iniciar sesión
                $user = Auth::getProvider()->retrieveByCredentials($credentials);
                
                // Verificar si el usuario pertenece a algún tenant
                $belongsToTenant = DB::table('organizacion_user')
                    ->where('user_id', $user->id)
                    ->exists();
                
                if ($belongsToTenant) {
                    // El usuario pertenece a un tenant, no permitimos login desde dominio principal
                    Notification::make()
                        ->title('Acceso restringido')
                        ->body("Debes acceder a través del subdominio de tu organización.")
                        ->warning()
                        ->persistent()
                        ->send();
                    
                    $this->addError('email', 'Acceso restringido para este usuario.');
                    
                    // Devolvemos null para evitar el inicio de sesión y cualquier redirección
                    return null;
                }
            }
        }

        // Procedemos con la autenticación normal
        return parent::authenticate();
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'email' => $data['email'],
            'password' => $data['password'],
        ];
    }

    public function getFooter(): string|Htmlable
    {
        $host = request()->getHost();

        if ($host !== 'multi-tenancy.test') {
            // Estamos en un subdominio de tenant
            $parts = explode('.', $host);
            $slug = $parts[0] ?? null;

            if ($slug && $tenant = Organizacion::where('slug', $slug)->first()) {
                return new HtmlString(
                    "<div class='mt-4 text-sm text-center'>
                        Accediendo a <strong>{$tenant->name}</strong>
                    </div>"
                );
            }
        }

        return parent::getFooter();
    }
}