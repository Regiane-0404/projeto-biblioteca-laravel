<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cart; // Importar o nosso modelo Cart
use Illuminate\Support\Carbon; // Importar a classe Carbon para lidar com datas
use Illuminate\Support\Facades\Mail; // Importar a Facade de Mail
use App\Mail\AbandonedCartReminder; // Importar o nosso Mailable
use Illuminate\Support\Facades\Log; // Para registar a atividade do comando

class CartNotifyAbandoned extends Command
{
    /**
     * O nome e a assinatura do comando da consola.
     * É assim que o chamamos: php artisan cart:notify-abandoned
     *
     * @var string
     */
    protected $signature = 'cart:notify-abandoned';

    /**
     * A descrição do comando da consola.
     *
     * @var string
     */
    protected $description = 'Envia emails de lembrete para carrinhos abandonados há mais de uma hora.';

    /**
     * Executa a lógica do comando.
     */
    public function handle()
    {
        // =======================================================
        // ==                 INÍCIO DA ALTERAÇÃO               ==
        // =======================================================
        // Vamos usar o helper 'activity()' que escreve na base de dados.
        // Ele também pode escrever nos logs se estiver configurado.
        activity()
            ->log('--- COMANDO DE CARRINHO ABANDONADO EXECUTADO ---');
        // =======================================================
        // ==                   FIM DA ALTERAÇÃO                  ==
        // =======================================================

        $this->info('A procurar carrinhos abandonados...');
        Log::info('Comando cart:notify-abandoned iniciado.');

        // ... (resto do seu método)
    }
}
