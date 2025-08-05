<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;

class EditTeamProfile extends EditTenantProfile
{

    public function mount(): void
    {
        $organizacion = \Filament\Facades\Filament::getTenant();

        $isOwner = $organizacion
            ->users()
            ->wherePivot('is_owner', true)
            ->where('users.id', auth()->id())
            ->exists();
            
        # Consulta directa a la tabla pivote a través del modelo de la tabla pivote que se creó
        #$isOwner = \App\Models\OrganizacionUser::where('organizacion_id', $organizacion->id)
            #->where('user_id', auth()->id())
            #->where('is_owner', true)
            #->exists();

        if (! $isOwner) {
            abort(403, 'No tienes permiso para editar este perfil.');
        }

        parent::mount();
    }

    public static function getLabel(): string
    {
        return 'Team profile';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                TextInput::make('slug'),
            ]);
    }
}