<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    protected $fillable = [
        'organizacion_id',
        'name',
        'percentage',
        'is_default'
    ];

    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class);
    }
}
