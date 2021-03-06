<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NovaSerieEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject = "Nova série adicionada";
    protected $nomeDaSerie;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($nomeDaSerie)
    {
        $this->nomeDaSerie = $nomeDaSerie;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.series.nova-serie-md', ['nomeDaSerie' => $this->nomeDaSerie]);
    }
}
