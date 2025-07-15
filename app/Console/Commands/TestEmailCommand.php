<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\TesteDeEmail; // Vamos criar este ficheiro a seguir

class TestEmailCommand extends Command
{
    protected $signature = 'app:test-email';
    protected $description = 'Envia um email de teste para verificar a configuração SMTP.';

    public function handle()
    {
        $destinatario = 'regianecinel@gmail.com'; // Enviar para si mesma para teste

        try {
            Mail::to($destinatario)->send(new TesteDeEmail());
            $this->info("✅ Email de teste enviado com sucesso para {$destinatario}!");
        } catch (\Exception $e) {
            $this->error("❌ Falha ao enviar o email.");
            $this->error("Erro: " . $e->getMessage());
        }
    }
}
