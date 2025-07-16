<?php

namespace App\Console\Commands;

use App\Models\Requisicao;
use App\Mail\LembreteDevolucao;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class EnviarLembretesDevolucao extends Command
{
    /**
     * A assinatura do comando no terminal.
     * É como chamamos o nosso trabalhador.
     */
    protected $signature = 'app:enviar-lembretes-devolucao';

    /**
     * A descrição do comando.
     */
    protected $description = 'Verifica as requisições que vencem amanhã e envia um email de lembrete.';

    /**
     * Executa a lógica do comando.
     */
    public function handle()
    {
        $this->info('A procurar por requisições que vencem amanhã...');

        // 1. Definimos a data de amanhã.
        $amanha = Carbon::tomorrow()->toDateString();

        // 2. Procuramos na base de dados por requisições que:
        //    - Tenham o status 'aprovado' (ainda estão com o leitor)
        //    - E cuja data de devolução seja igual a amanhã.
        $requisicoes = Requisicao::where('status', 'aprovado')
            ->whereDate('data_fim_prevista', $amanha)
            ->with(['user', 'livro']) // Carregamos os dados para usar no email
            ->get();

        // 3. Verificamos se encontrámos alguma.
        if ($requisicoes->isEmpty()) {
            $this->info('Nenhuma requisição encontrada para enviar lembretes hoje.');
            return 0; // Termina o comando com sucesso.
        }

        $this->info("Encontrámos {$requisicoes->count()} requisição(ões) para notificar.");

        // 4. Para cada requisição encontrada, enviamos o email.
        foreach ($requisicoes as $requisicao) {
            Mail::to($requisicao->user->email)->send(new LembreteDevolucao($requisicao));
            $this->info("Lembrete enviado para: {$requisicao->user->email} (Requisição #{$requisicao->numero_sequencial})");
        }

        $this->info('✅ Todos os lembretes foram enviados com sucesso!');
        return 0; // Termina o comando com sucesso.
    }
}
