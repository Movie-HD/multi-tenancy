<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        $data["user_id"] = $user->id;
        $data["organizacion_id"] = request()->route("tenant")?->id;

        // Si el usuario solo tiene una sucursal, asignarla automáticamente
        if ($user instanceof User) {
            $userSucursales = $user->sucursales();
            if ($userSucursales->count() === 1) {
                $data["sucursal_id"] = $userSucursales->first()->getKey();
            }
        }

        return $data;
    }
}
