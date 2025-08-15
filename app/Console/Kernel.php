<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */

    protected function schedule(Schedule $schedule)
    {
        // --- Tarefa Existente ---
        // Executa o comando para enviar lembretes de devolução todos os dias às 9:00.
        $schedule->command('app:enviar-lembretes-devolucao')->dailyAt('09:00');
        //$schedule->command('app:enviar-lembretes-devolucao')->everyMinute();

        // ==============================================================
        // ==              INÍCIO DA NOSSA NOVA TAREFA                 ==
        // ==============================================================
        // Executa o comando para notificar carrinhos abandonados
        // a cada dez minutos.
        $schedule->command('cart:notify-abandoned')->everyTenMinutes();
        // ==============================================================
        // ==                FIM DA NOSSA NOVA TAREFA                  ==
        // ==============================================================
    }
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
