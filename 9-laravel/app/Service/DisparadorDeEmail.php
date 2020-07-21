<?php

namespace App\Service;

use App\Mail\NovaSerie;
use App\Serie;
use App\User;
use Illuminate\Support\Facades\Mail;

class DisparadorDeEmail
{
    public function enviaEmailNovaSerie(Serie $novaSerie): void
    {
        $multiplicador = 0;
        User::all()->each(function (User $usuario) use ($novaSerie, &$multiplicador) {

            Mail::to($usuario)->later(
                now()->addSeconds(5 * $multiplicador++),
                new NovaSerie($novaSerie->nome)
            );
        });

        return;
    }
}
