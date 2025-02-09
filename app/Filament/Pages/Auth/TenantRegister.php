<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Register;
use App\Models\Organizacion;
use Illuminate\Database\Eloquent\Model;

class TenantRegister extends Register
{
    public ?string $tenantSlug = null;

    public function mount(): void
    {
        // Obtiene el slug del tenant de la URL
        $this->tenantSlug = request()->segment(2);

        // Si es el registro inicial (/dashboard/register), permitir continuar
        if ($this->tenantSlug === 'register') {
            parent::mount();
            return;
        }

        // Para rutas de tenant, validar que exista
        if (!$this->tenantSlug || !Organizacion::where('slug', $this->tenantSlug)->exists()) {
            abort(404, 'Organización no encontrada');
        }

        parent::mount();
    }

    protected function handleRegistration(array $data): Model
    {
        $user = parent::handleRegistration($data);

        // Solo adjuntar al tenant si no es el registro inicial
        if ($this->tenantSlug !== 'register') {
            $tenant = Organizacion::where('slug', $this->tenantSlug)->first();
            $tenant->users()->attach($user);
        }

        return $user;
    }

    public function getTitle(): string 
    {
        // Título personalizado según el tipo de registro
        if ($this->tenantSlug === 'register') {
            return 'Registro Inicial';
        }

        $tenant = Organizacion::where('slug', $this->tenantSlug)->first();
        return "Registro para {$tenant->name}";
    }
}
