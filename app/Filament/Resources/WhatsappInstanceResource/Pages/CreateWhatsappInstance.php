<?php

namespace App\Filament\Resources\WhatsappInstanceResource\Pages;

use App\Filament\Resources\WhatsappInstanceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Facades\Filament;
use App\Models\Sucursal;

class CreateWhatsappInstance extends CreateRecord
{
    protected static string $resource = WhatsappInstanceResource::class;

    protected function afterCreate(): void
    {
        $tenant = Filament::getTenant();
        $sucursalCount = Sucursal::where('organizacion_id', $tenant->id)->count();

        if ($sucursalCount === 1) {
            $sucursal = Sucursal::where('organizacion_id', $tenant->id)->first();
            $this->record->sucursales()->attach($sucursal->id);
        }

        // Generar QR automáticamente después de crear la instancia
        static::$resource::generateQR($this->record);
    }

}
