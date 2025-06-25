<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        "title",
        "description",
        "user_id",
        "organizacion_id",
        "sucursal_id",
        "completed",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}
