<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Cria uma nova instância do Mailable.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Constrói o conteúdo do e-mail.
     */
    public function build()
    {
        return $this->subject('🎉 Bem-vindo à Igreja São João Baptista do Fomento')
                    ->view('emails.welcome') // usa o HTML personalizado
                    ->with(['user' => $this->user]);
    }
}
