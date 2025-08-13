<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organizacion_id',
        'user_id',
        'name',
        'last_name',
        'phone',
        'email',
        'address',
    ];

    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
