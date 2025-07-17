<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'organizacion_id',
        'code',
        'symbol',
        'is_default'
    ];

    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class);
    }
}
