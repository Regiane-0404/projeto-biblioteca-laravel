<?php

// --- CORREÇÃO 1: Namespace correto, sem hífen ---
namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AdminLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with(['causer', 'subject'])->latest();

        // Aplica os filtros
        if ($request->filled('data_de')) {
            $query->whereDate('created_at', '>=', $request->data_de);
        }
        if ($request->filled('data_ate')) {
            $query->whereDate('created_at', '<=', $request->data_ate);
        }
        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }

        $filtros = $request->only(['data_de', 'data_ate', 'log_name']);

        // =======================================================
        // ==         LÓGICA DE DECISÃO FINAL (CORRETA)         ==
        // =======================================================
        if (empty(array_filter($filtros))) {
            // SEM FILTROS: Pega apenas os 10 mais recentes.
            $logs = $query->take(10)->get();
        } else {
            // COM FILTROS: Pagina os resultados.
            $logs = $query->paginate(20);
        }
        // =======================================================
        // ==                                                   ==
        // =======================================================

        $modulos = Activity::select('log_name')->distinct()->orderBy('log_name')->pluck('log_name');

        return view('admin.logs.index', compact('logs', 'filtros', 'modulos'));
    }
}
