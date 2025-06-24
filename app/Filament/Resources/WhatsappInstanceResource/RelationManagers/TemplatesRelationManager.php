<?php

namespace App\Filament\Resources\WhatsappInstanceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput; # Agregar si es un Input [Form]
use Filament\Tables\Columns\TextColumn; # Agregar si es un Column [Table]

class TemplatesRelationManager extends RelationManager
{
    protected static string $relationship = 'templates';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')
                    ->required()
                    # Deshabilita la edición del nombre si la plantilla es "is_default"
                    ->disabled(fn ($record) => $record?->is_default),
                TextInput::make('mensaje')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
            ->columns([
                TextColumn::make('nombre'),
                TextColumn::make('mensaje'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    # Oculta la acción de eliminar para plantillas "is_default"
                    ->visible(fn ($record) => !$record->is_default),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            # Deshabilita la selección de checkboxes para plantillas "is_default" en acciones masivas
            ->checkIfRecordIsSelectableUsing(fn ($record) => !$record->is_default);
    }
}
