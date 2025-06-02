<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorito extends Model
{
    protected $fillable = ['user_id', 'inmueble_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function inmueble() {
        return $this->belongsTo(Inmueble::class);
    }
}
