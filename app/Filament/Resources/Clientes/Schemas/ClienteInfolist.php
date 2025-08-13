<?php

namespace App\Filament\Resources\Clientes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ClienteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('Creado por')
                    ->visible(
                        fn() => Auth::user() instanceof User &&
                            Auth::user()->can_view_all
                    ),
                TextEntry::make('name'),
                TextEntry::make('last_name'),
                TextEntry::make('phone'),
                TextEntry::make('email'),
                TextEntry::make('address'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
