<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Organizacion;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Support\Facades\Artisan; # servira para crear el super admin

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

        // Create super admin for this tenant
        Artisan::call('shield:super-admin', [
            '--user' => auth()->id(),
            '--tenant' => $team->id
        ]);

        return $team;
    }
}
