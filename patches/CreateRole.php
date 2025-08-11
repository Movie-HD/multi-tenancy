<?php

namespace BezhanSalleh\FilamentShield\Resources\RoleResource\Pages;

use BezhanSalleh\FilamentShield\Resources\RoleResource;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    public Collection $permissions;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->permissions = collect($data)
            ->filter(function (mixed $permission, string $key): bool {
                return ! in_array($key, ['name', 'guard_name', 'select_all', Utils::getTenantModelForeignKey()]);
            })
            ->values()
            ->flatten()
            ->unique();

        if (Arr::has($data, Utils::getTenantModelForeignKey())) {
            return Arr::only($data, ['name', 'guard_name', Utils::getTenantModelForeignKey()]);
        }

        return Arr::only($data, ['name', 'guard_name']);
    }

    /**
     * Sobrescribe el método handleRecordCreation para agregar el organizacion_id al array $data
     * @author Cristian
     */
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // 1. Obtener el ID de la organización actual
        $currentTenantId = \Filament\Facades\Filament::getTenant()?->id;

        // 2. Asignar el 'organizacion_id' directamente al array $data
        // Esto sobrescribirá cualquier valor existente o establecerá el valor correcto.
        if ($currentTenantId !== null) {
            $data['organizacion_id'] = $currentTenantId;
            // Opcional: Si 'team_id' también se usa y es el mismo ID, asigna también:
            // $data['team_id'] = $currentTenantId;
        } else {
            // Si no se pudo determinar la organización, podemos detener la creación
            // o lanzar una excepción, dependiendo de si el organizacion_id es obligatorio.
            \Filament\Notifications\Notification::make()
                ->danger()
                ->title('Error de Organización')
                ->body('No se pudo determinar la organización para asignar el rol. Asegúrate de estar asociado a una organización.')
                ->send();
            $this->halt(); // Detiene el proceso de creación.
            // Si $this->halt() se ejecuta, la línea 'return static::getModel()::create($data);' nunca se alcanzará.
        }

        // 3. Validaciones adicionales si las necesitas (ej. duplicados)
        // Puedes mover la lógica de verificación de rol duplicado aquí si lo prefieres,
        // pero beforeCreate() sigue siendo un buen lugar para validaciones que detienen el proceso antes de la creación.
        // Si ya lo tienes en beforeCreate(), no lo repitas aquí.

        // 4. Crear el modelo de Rol con los datos modificados
        return static::getModel()::create($data);
    }
    /** END code Cristian */

    protected function afterCreate(): void
    {
        $permissionModels = collect();
        $this->permissions->each(function (string $permission) use ($permissionModels): void {
            $permissionModels->push(Utils::getPermissionModel()::firstOrCreate([
                /** @phpstan-ignore-next-line */
                'name' => $permission,
                'guard_name' => $this->data['guard_name'],
            ]));
        });

        $this->record->syncPermissions($permissionModels);
    }

    /**
     * Valida si existe un rol con el mismo nombre en el tenant actual
     * Si existe, muestra notificación y detiene la creación
     * @author Cristian
     */
    protected function beforeCreate(): void
    {
        $existingRole = \Spatie\Permission\Models\Role::query()
            ->whereRaw('LOWER(name) = ?', [strtolower($this->data['name'])])
            ->where('team_id', \Filament\Facades\Filament::getTenant()->id)
            ->first();

        if ($existingRole) {
            \Filament\Notifications\Notification::make()
                ->danger()
                ->title('Error')
                ->body('Ya existe un rol con este nombre en esta organización')
                ->send();

            $this->halt();
        }
    }
    /** END code Cristian */
}
