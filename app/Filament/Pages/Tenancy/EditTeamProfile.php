<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Actions\Action;

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
                Repeater::make('sucursals')
                    ->relationship('sucursals')
                    ->schema([
                        TextInput::make('nombre')->label('Nombre')->required(),
                        TextInput::make('direccion')->label('Dirección'),
                        TextInput::make('telefono')->label('Teléfono'),
                        TextInput::make('email')->label('Email'),
                        TextInput::make('web')->label('Web'),
                    ])
                    ->label('Sucursales')
                    ->hiddenLabel()
                    ->columns(2)
                    ->addable(false)
                    ->addActionLabel('Agregar sucursal')
                    ->deleteAction(
                        fn (Action $action) => $action->requiresConfirmation(),
                    )
                    # No filtramos por organizacion(tenant) ya que el Repeater solo muestra
                    # y manipula sucursales del tenant (organización) actual,
                    # gracias a la relación ->relationship('sucursals') 
                    ->deletable(fn ($record) => $record->sucursals()->count() > 1),
            ]);
    }
}