<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inmueble extends Model
{
    use HasFactory;

    protected $table = "inmueble";

    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'tipo',
        'operacion',
        'titulo',
        'foto',
        'direccion',
        'precio',
        'habitacion',
        'metro',
        'descripcion',
        'destacado',
        'activo'
    ];

    /**
     * Obtener el usuario propietario del inmueble
     */
    public function propietario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
