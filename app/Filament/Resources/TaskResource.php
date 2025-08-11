<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Tables\Filters\Filter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\TaskResource\Pages\ListTasks;
use App\Filament\Resources\TaskResource\Pages\CreateTask;
use App\Filament\Resources\TaskResource\Pages\EditTask;
use App\Filament\Resources\TaskResource\Pages;
use App\Models\Task;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament; # Se agrega para obtener solo los roles del tenant actual

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static string | \BackedEnum | null $navigationIcon = "heroicon-s-rectangle-stack";
    protected static ?string $tenantRelationshipName = "tasks";

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make("title")
                ->label("Título")
                ->required()
                ->maxLength(255),

            Textarea::make("description")
                ->label("Descripción")
                ->rows(3)
                ->maxLength(65535)
                ->nullable(),

            Select::make("sucursal_id")
                ->label("Sucursal")
                ->required()
                ->relationship('sucursal', 'nombre', modifyQueryUsing: function (Builder $query) {
                    $tenant = Filament::getTenant(); // Obtiene el modelo del inquilino actual (Organizacion)
                    $user = Auth::user(); // Obtiene el usuario autenticado

                    // Asegúrate de que el usuario es una instancia de tu modelo User
                    if ($user instanceof User) {
                        // Obtiene los IDs de las sucursales asociadas directamente con el usuario
                        // Se especifica 'sucursals.id' para evitar la ambigüedad de la columna 'id'
                        // que puede ocurrir en relaciones muchos-a-muchos con la tabla pivote.
                        $userSucursalIds = $user->sucursales()->pluck('sucursals.id')->toArray();

                        // Filtra las sucursales:
                        // 1. Deben pertenecer al inquilino (organización) actual.
                        //    Esto asume que tu modelo Sucursal tiene una relación de belongsTo con el modelo de inquilino.
                        // 2. Sus IDs deben estar en la lista de IDs de sucursales a las que el usuario tiene acceso.
                        $query->whereBelongsTo($tenant)
                              ->whereIn('id', $userSucursalIds);
                    }
                    return $query;
                })
                ->preload()
                ->searchable()
                ->visible(function () {
                    $user = auth()->user();
                    $tenant = Filament::getTenant();

                    return $user->sucursales()
                        ->where('organizacion_id', $tenant->id)
                        ->count() > 1;
                }),

            Toggle::make("completed")->label("Completada")->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("title")
                    ->label("Título")
                    ->sortable()
                    ->searchable(),

                TextColumn::make("user.name")
                    ->label("Creado por")
                    ->sortable()
                    ->searchable()
                    ->visible(
                        fn() => Auth::user() instanceof User &&
                            Auth::user()->can_view_all
                    ),

                TextColumn::make("sucursal.nombre")
                    ->label("Sucursal")
                    ->sortable()
                    ->searchable(),

                IconColumn::make("completed")
                    ->label("Completada")
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make("sucursal_id")
                    ->label("Sucursal")
                    ->relationship("sucursal", "nombre")
                    ->visible(
                        fn() => Auth::user() instanceof User &&
                            Auth::user()->can_view_all
                    ),

                Filter::make("completed")
                    ->label("Completadas")
                    ->query(
                        fn(Builder $query): Builder => $query->where(
                            "completed",
                            true
                        )
                    ),

                Filter::make("pending")
                    ->label("Pendientes")
                    ->query(
                        fn(Builder $query): Builder => $query->where(
                            "completed",
                            false
                        )
                    ),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([DeleteBulkAction::make()]);
    }

    public static function getRelations(): array
    {
        return [
                //
            ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = Auth::user();

        if ($user instanceof User) {
            // Filtrar por sucursales del usuario
            $userSucursales = $user
                ->sucursales()
                ->select("sucursals.id")
                ->pluck("id");
            $query->whereIn("sucursal_id", $userSucursales);

            // Si el usuario no puede ver todos los registros, solo mostrar los que él creó
            if (!$user->can_view_all) {
                $query->where("user_id", $user->id);
            }
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            "index" => ListTasks::route("/"),
            "create" => CreateTask::route("/create"),
            "edit" => EditTask::route("/{record}/edit"),
        ];
    }
}
