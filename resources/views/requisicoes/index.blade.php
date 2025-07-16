<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">📋 Requisições</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div role="alert" class="alert alert-success mb-6"><span>{{ session('success') }}</span></div>
            @endif
            @if (session('error'))
                <div role="alert" class="alert alert-error mb-6"><span>{{ session('error') }}</span></div>
            @endif

            @if (auth()->user()->role === 'admin')
                <!-- ABAS COM ESTILO DE BOTÃO E CORES PADRONIZADAS -->
                <div class="join mb-6">
                    <!-- Aba 1: Visão Geral (Verde quando ativa) -->
                    <a class="join-item btn btn-sm {{ ($active_tab ?? 'visao_geral') === 'visao_geral' ? 'btn-active btn-success' : 'btn-ghost' }}"
                        href="{{ route('requisicoes.index') }}">
                        Visão Geral
                    </a>
                    <!-- Aba 2: Lista Completa (Verde quando ativa) -->
                    <a class="join-item btn btn-sm {{ ($active_tab ?? '') === 'lista' ? 'btn-active btn-success' : 'btn-ghost' }}"
                        href="{{ route('requisicoes.index', ['tab' => 'lista']) }}">
                        Lista Completa
                    </a>
                </div>

                @if (($active_tab ?? 'visao_geral') === 'visao_geral')
                    <!-- ============================================= -->
                    <!--     CONTEÚDO DA ABA "VISÃO GERAL" ATUALIZADO    -->
                    <!-- ============================================= -->
                    <div class="space-y-6">
                        <!-- Painel de Indicadores -->
                        <div class="bg-base-100 rounded-box p-6 shadow-xl">
                            <h2 class="text-2xl font-bold mb-4">Dados Gerais</h2>
                            <div class="stats shadow w-full stats-vertical lg:stats-horizontal">
                                <div class="stat">
                                    <div class="stat-title">Requisições Ativas</div>
                                    <div class="stat-value text-info">{{ $stats['ativas'] ?? 0 }}</div>
                                </div>
                                <div class="stat">
                                    <div class="stat-title">Novas (Últimos 30 dias)</div>
                                    <div class="stat-value">{{ $stats['ultimos_30_dias'] ?? 0 }}</div>
                                </div>
                                <div class="stat">
                                    <div class="stat-title">Devolvidos Hoje</div>
                                    <div class="stat-value text-success">{{ $stats['devolvidos_hoje'] ?? 0 }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Painel de Links Rápidos -->
                        <div class="card bg-base-100 shadow-xl">
                            <div class="card-body">
                                <h3 class="card-title">🔗 Links Úteis</h3>
                                <ul class="list-disc list-inside mt-2 space-y-1">
                                    <li><a href="https://www.dge.mec.pt/" target="_blank"
                                            class="link link-hover text-primary">Direção-Geral da Educação (DGE)</a>
                                    </li>
                                    <li><a href="https://www.pnl2027.gov.pt/" target="_blank"
                                            class="link link-hover text-primary">Plano Nacional de Leitura 2027</a></li>
                                    <li><a href="https://www.bnportugal.gov.pt/" target="_blank"
                                            class="link link-hover text-primary">Biblioteca Nacional de Portugal</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- CONTEÚDO DA ABA "LISTA COMPLETA" -->
                    <div class="bg-base-100 rounded-box p-6 shadow-xl">
                        @include('requisicoes.partials.admin-lista-completa')
                    </div>
                @endif
            @else
                <!-- VISÃO SIMPLES PARA CIDADÃO -->
                <div class="mb-6"><a href="{{ route('requisicoes.create') }}" class="btn btn-primary">➕ Fazer Nova
                        Requisição</a></div>
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        @include('requisicoes.partials.cidadao-lista')
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
