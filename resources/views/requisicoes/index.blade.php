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
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div role="alert" class="alert alert-error mb-6 shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
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

            <!-- ======================================================= -->
            <!--   NOVO CARD DE FILTROS (APENAS PARA ADMIN) - ADICIONAR  -->
            <!-- ======================================================= -->
            @if (auth()->user()->role === 'admin')
                <div class="card bg-base-200 shadow-md mb-6">
                    <div class="card-body">
                        <form method="GET" action="{{ route('requisicoes.index') }}">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

                                <!-- Filtro Data De -->
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Requisições Desde:</span></label>
                                    <input type="date" name="data_de" value="{{ $filtro_data_de ?? '' }}"
                                        class="input input-bordered w-full">
                                </div>

                                <!-- Filtro Data Até -->
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Até:</span></label>
                                    <input type="date" name="data_ate" value="{{ $filtro_data_ate ?? '' }}"
                                        class="input input-bordered w-full">
                                </div>

                                <!-- Filtro por Status -->
                                <div class="form-control">
                                    <label class="label"><span class="label-text">Filtrar por Status:</span></label>
                                    <select name="status" class="select select-bordered w-full">
                                        <option value="">Todos os Status</option>
                                        <option value="solicitado" @if ($filtro_status == 'solicitado') selected @endif>
                                            Solicitado</option>
                                        <option value="aprovado" @if ($filtro_status == 'aprovado') selected @endif>
                                            Aprovado (Em posse)</option>
                                        <option value="devolvido" @if ($filtro_status == 'devolvido') selected @endif>
                                            Devolvido</option>
                                        <option value="cancelado" @if ($filtro_status == 'cancelado') selected @endif>
                                            Cancelado</option>
                                    </select>
                                </div>

                                <!-- Botões de Ação -->
                                <div class="flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-grow">🔍 Filtrar</button>
                                    <a href="{{ route('requisicoes.index') }}" class="btn btn-ghost"
                                        title="Limpar Filtros e Ver Hoje">🔄</a>
                                </div>

                            </div>
                        </form>
                    </div>
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
                                        <td class="font-semibold">{{ $requisicao->livro->nome ?? 'Livro Removido' }}
                                        </td>

                                        @if (Auth::user()->role === 'admin')
                                            <td>{{ $requisicao->user->name ?? 'Usuário Removido' }}</td>
                                        @endif

                                        <td>
                                            @if ($requisicao->status === 'solicitado')
                                                <span class="badge badge-warning">🟡 Solicitado</span>
                                            @elseif ($requisicao->status === 'aprovado')
                                                <span class="badge badge-info">🔵 Em posse</span>
                                            @elseif ($requisicao->status === 'devolvido')
                                                <span class="badge badge-success">✅ Devolvido</span>
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
                                                        <form action="{{ route('requisicoes.aprovar', $requisicao) }}"
                                                            method="POST" class="inline">
                                                            @csrf @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-success"
                                                                title="Aprovar">✅</button>
                                                        </form>
                                                    @endif
                                                    @if ($requisicao->status === 'aprovado')
                                                        <button class="btn btn-sm btn-info"
                                                            onclick="devolucao_modal_{{ $requisicao->id }}.showModal()">
                                                            📚 Registrar Devolução
                                                        </button>

                                                        <dialog id="devolucao_modal_{{ $requisicao->id }}"
                                                            class="modal">
                                                            <div class="modal-box">
                                                                <h3 class="font-bold text-lg">Registrar Devolução do
                                                                    Livro</h3>
                                                                <p class="py-2 text-sm">Livro: <span
                                                                        class="font-semibold">{{ $requisicao->livro->nome_visivel ?? 'N/A' }}</span>
                                                                </p>

                                                                <form method="POST"
                                                                    action="{{ route('requisicoes.entregar', $requisicao) }}"
                                                                    class="mt-4 space-y-4">
                                                                    @csrf
                                                                    @method('PATCH')

                                                                    <div>
                                                                        <label class="label">
                                                                            <span class="label-text">Data de Devolução
                                                                                Real</span>
                                                                        </label>
                                                                        <input type="date" name="data_fim_real"
                                                                            class="input input-bordered w-full"
                                                                            value="{{ now()->format('Y-m-d') }}"
                                                                            required>
                                                                    </div>

                                                                    <div>
                                                                        <label class="label">
                                                                            <span class="label-text">Observações
                                                                                (opcional)
                                                                            </span>
                                                                        </label>
                                                                        <textarea name="observacoes" class="textarea textarea-bordered w-full"
                                                                            placeholder="Ex: Livro devolvido com uma pequena marca na capa..."></textarea>
                                                                    </div>

                                                                    <div class="modal-action">
                                                                        <button type="button" class="btn"
                                                                            onclick="document.getElementById('devolucao_modal_{{ $requisicao->id }}').close()">Cancelar</button>
                                                                        <button type="submit"
                                                                            class="btn btn-primary">Confirmar
                                                                            Devolução</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                            <form method="dialog" class="modal-backdrop">
                                                                <button>close</button>
                                                            </form>
                                                        </dialog>
                                                    @endif
                                                @else
                                                    {{-- Ações do CIDADÃO --}}
                                                    @if ($requisicao->status === 'solicitado')
                                                        <form
                                                            action="{{ route('requisicoes.cancelar', $requisicao) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Tem a certeza que deseja cancelar o seu pedido?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-sm btn-outline btn-error">Cancelar
                                                                Pedido</button>
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
                                        <td colspan="{{ Auth::user()->role === 'admin' ? '7' : '6' }}"
                                            class="text-center py-8">
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
