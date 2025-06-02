<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    use HasFactory;

    protected $table = "contactos";

    protected $fillable = [
        'inmueble_id',
        'propietario_id',
        'nombre',
        'email',
        'telefono',
        'mensaje',
        'leido'
    ];

    /**
     * Obtener el inmueble asociado al contacto
     */
    public function inmueble()
    {
        return $this->belongsTo(Inmueble::class, 'inmueble_id');
    }

    /**
     * Obtener el propietario al que se envió el contacto
     */
    public function propietario()
    {
        return $this->belongsTo(User::class, 'propietario_id');
    }
}
