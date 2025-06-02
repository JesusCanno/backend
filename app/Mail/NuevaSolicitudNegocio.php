<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\SolicitudNegocio;

class NuevaSolicitudNegocio extends Mailable
{
    use Queueable, SerializesModels;

    public $solicitud;

    public function __construct(SolicitudNegocio $solicitud)
    {
        $this->solicitud = $solicitud;
    }

    public function build()
    {
        return $this->subject('Nueva solicitud de cuenta empresarial')
            ->view('emails.nueva_solicitud_negocio');
    }
}
