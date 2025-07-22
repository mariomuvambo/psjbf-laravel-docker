<?php

namespace App\Mail;

use App\Models\Aviso;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AvisoNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $aviso;

    public function __construct(Aviso $aviso)
    {
        $this->aviso = $aviso;
    }

    public function build()
    {
        return $this->view('emails.aviso')
                    ->with([ 
                        'title' => $this->aviso->title,
                        'date_notify' => $this->aviso->date_notify->format('d/m/Y'),
                        'address' => $this->aviso->address,
                        'date_realize' => $this->aviso->date_realize->format('d/m/Y'),
                        'hora' => $this->aviso->hora,
                        'description' => $this->aviso->description,
                    ])
                    ->subject('Novo Aviso: ' . $this->aviso->title);
    }
}