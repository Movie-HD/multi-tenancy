<?php

namespace App\Filament\Resources\TaskResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\TaskResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class EditTask extends EditRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function authorizeAccess(): void
    {
        parent::authorizeAccess();

        $user = Auth::user();

        if ($user instanceof User) {
            // Verificar que el usuario tenga acceso a la sucursal de la tarea
            $userSucursales = $user
                ->sucursales()
                ->select("sucursals.id")
                ->pluck("id");
            if (!$userSucursales->contains($this->record->sucursal_id)) {
                throw new AccessDeniedHttpException(
                    "No tienes permisos para acceder a tareas de esta sucursal."
                );
            }

            // Si el usuario no puede ver todos los registros, verificar que sea el propietario
            if (!$user->can_view_all && $this->record->user_id !== $user->id) {
                throw new AccessDeniedHttpException(
                    "No tienes permisos para editar esta tarea."
                );
            }
        }
    }
}
