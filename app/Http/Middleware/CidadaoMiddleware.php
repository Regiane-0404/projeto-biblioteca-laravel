<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CidadaoMiddleware
{
    public function handle(Request $request, Closure $next): Response
{
    if (Auth::check() && Auth::user()->role === 'cidadao' && Auth::user()->ativo) {
        return $next($request);
    }

    if (Auth::check() && !Auth::user()->ativo) {
        // Usar método correto para logout
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Auth::guard('web')->logout();
        
        return redirect()->route('login')->with('error', 'Sua conta foi inativada. Entre em contato com o administrador.');
    }

    abort(403, 'Acesso negado. Apenas cidadãos.');
}
}