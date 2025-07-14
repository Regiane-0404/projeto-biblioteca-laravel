<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            📋 {{ Auth::user()->role === 'admin' ? 'Todas as Requisições' : 'Minhas Requisições' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensagens de Feedback -->
            @if (session('success'))
                <div role="alert" class="alert alert-success mb-6 shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div role="alert" class="alert alert-error mb-6 shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Botão Nova Requisição para Cidadão -->
            @if (Auth::user()->role === 'cidadao')
                <div class="mb-6">
                    <a href="{{ route('requisicoes.create') }}" class="btn btn-primary">
                        ➕ Fazer Nova Requisição
                    </a>
                </div>
            @endif

            <!-- Tabela de Requisições -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Livro</th>
                                    @if (Auth::user()->role === 'admin')
                                        <th>Cidadão</th>
                                    @endif
                                    <th>Status</th>
                                    <th>Data Início</th>
                                    <th>Data Fim (Prevista)</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requisicoes as $requisicao)
                                    <tr>
                                        <td class="font-mono text-sm">{{ $requisicao->numero_sequencial }}</td>
                                        <td class="font-semibold">{{ $requisicao->livro->nome ?? 'Livro Removido' }}</td>
                                        
                                        @if (Auth::user()->role === 'admin')
                                            <td>{{ $requisicao->user->name ?? 'Usuário Removido' }}</td>
                                        @endif

                                        <td>
                                            @if ($requisicao->status === 'solicitado')
                                                <span class="badge badge-warning">🟡 Solicitado</span>
                                            @elseif ($requisicao->status === 'aprovado')
                                                <span class="badge badge-info">🔵 Aprovado</span>
                                            @elseif ($requisicao->status === 'entregue' || $requisicao->status === 'devolvido')
                                                <span class="badge badge-success">✅ Concluído</span>
                                            @elseif ($requisicao->status === 'cancelado')
                                                <span class="badge badge-ghost">⚪ Cancelado</span>
                                            @endif
                                        </td>
                                        
                                        <td>{{ optional($requisicao->data_inicio)->format('d/m/Y') }}</td>
                                        <td>{{ optional($requisicao->data_fim_prevista)->format('d/m/Y') }}</td>
                                        
                                        <td>
                                            <div class="flex gap-2">
                                                @if (Auth::user()->role === 'admin')
                                                    {{-- Ações do ADMIN --}}
                                                    @if ($requisicao->status === 'solicitado')
                                                        <form action="{{ route('requisicoes.aprovar', $requisicao) }}" method="POST" class="inline">
                                                            @csrf @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-success" title="Aprovar">✅</button>
                                                        </form>
                                                    @endif
                                                    @if ($requisicao->status === 'aprovado')
                                                        <form action="{{ route('requisicoes.entregar', $requisicao) }}" method="POST" class="inline">
                                                            @csrf @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-info" title="Confirmar Entrega">📚</button>
                                                        </form>
                                                    @endif
                                                @else
                                                    {{-- Ações do CIDADÃO --}}
                                                    @if ($requisicao->status === 'solicitado')
                                                        {{-- USAMOS A MESMA ROTA DE CANCELAR, O CONTROLLER VAI DECIDIR --}}
                                                        <form action="{{ route('requisicoes.cancelar', $requisicao) }}" method="POST" onsubmit="return confirm('Tem a certeza que deseja cancelar o seu pedido?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline btn-error">Cancelar Pedido</button>
                                                        </form>
                                                    @else
                                                        <span class="text-gray-400 text-sm">-</span>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ Auth::user()->role === 'admin' ? '7' : '6' }}" class="text-center py-8">
                                            <p class="text-lg text-base-content/50">Nenhuma requisição encontrada.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $requisicoes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>