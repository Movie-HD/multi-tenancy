<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Organizacion;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;

class RegisterTeam extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Registrar Organizacion';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                TextInput::make('slug'),
            ]);
    }

    protected function handleRegistration(array $data): Organizacion
    {
        $team = Organizacion::create($data);

        $team->users()->attach(auth()->user());

        return $team;
    }
}
