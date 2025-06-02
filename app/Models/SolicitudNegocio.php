<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudNegocio extends Model
{
    protected $table = 'solicitudes_negocio';
    protected $fillable = [
        'company_name',
        'contact_person',
        'email',
        'phone',
        'business_type',
        'employees',
        'description',
        'services',
    ];
}
