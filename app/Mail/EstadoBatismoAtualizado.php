<?php

namespace App\Mail;

use App\Models\Batismo;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EstadoBatismoAtualizado extends Mailable
{
    use Queueable, SerializesModels;

    public $batismo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Batismo $batismo)
    {
        $this->batismo = $batismo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Atualização no estado do seu pedido de Batismo')
                    ->markdown('emails.batismo.estado');
    }
}
