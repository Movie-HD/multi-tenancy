<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

# ¿Cuándo necesitas un modelo para la tabla pivote?
# NO es obligatorio crear un modelo para una tabla pivote, incluso si tiene campos extra.
# Laravel maneja automáticamente las tablas pivote con métodos como belongsToMany()->withPivot('campo').
# Si solo necesitas acceder a los campos extra de la tabla pivote en las relaciones muchos a muchos, puedes usar ->withPivot('is_owner') en las relaciones y acceder a esos campos a través de $pivot.

# Solo necesitas un modelo (por ejemplo, OrganizacionUser) si quieres:
# - Agregar métodos personalizados o lógica específica a la relación pivote.
# - Usar eventos de Eloquent (creating, updating, etc.) en la tabla pivote.
# - Usar la relación "custom pivot model" con el método ->using(ModeloPivote::class) para acceder a métodos/atributos como si fuera un modelo normal.
# - Si quieres consultar, filtrar o manipular directamente la tabla pivote como si fuera un modelo Eloquent (por ejemplo, OrganizacionUser::where('is_owner', true)->get();), entonces necesitas tener el modelo OrganizacionUser.

class OrganizacionUser extends Model
{
    # Laravel espera que la tabla pivote se llame organizacion_users (plural),
    # y con la propiedad $table se especifica el nombre correcto.
    protected $table = 'organizacion_user';

    protected $fillable = [
        'organizacion_id',
        'user_id',
        'is_owner'
    ];

    protected $casts = [
        'is_owner' => 'boolean',
    ];

}
