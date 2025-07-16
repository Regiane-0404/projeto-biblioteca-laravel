<!-- Ficheiro: resources/views/requisicoes/partials/admin-lista-completa.blade.php -->

<!-- Card de Filtros -->
<div class="card bg-base-200 shadow-md mb-6">
    <div class="card-body">
        <form method="GET" action="{{ route('requisicoes.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div class="form-control">
                    <label class="label"><span class="label-text">Desde:</span></label>
                    <input type="date" name="data_de" value="{{ $filtro_data_de ?? '' }}" class="input input-bordered w-full">
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Até:</span></label>
                    <input type="date" name="data_ate" value="{{ $filtro_data_ate ?? '' }}" class="input input-bordered w-full">
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Status:</span></label>
                    <!-- DROPDOWN DE STATUS CORRIGIDO -->
                    <select name="status" class="select select-bordered w-full" onchange="this.form.submit()">
                        <option value="">Todos os Status</option>
                        <option value="solicitado" @if(request('status') == 'solicitado') selected @endif>Solicitado</option>
                        <option value="aprovado" @if(request('status') == 'aprovado') selected @endif>Em posse</option>
                        <option value="devolvido" @if(request('status') == 'devolvido') selected @endif>Devolvido</option>
                        {{-- <option value="cancelado" @if(request('status') == 'cancelado') selected @endif>Cancelado</option> --}}
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow">🔍 Filtrar</button>
                    <a href="{{ route('requisicoes.index', ['tab' => 'lista']) }}" class="btn btn-ghost" title="Limpar Filtros">🔄</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabela de Requisições -->
<div class="overflow-x-auto">
    <table class="table w-full">
        <thead>
            <tr>
                <th>Número</th>
                <th>Livro</th>
                <th>Cidadão</th>
                <th class="text-center">Status</th>
                <th>Data Início</th>
                <th>Data Fim (Prevista)</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requisicoes as $requisicao)
                @php
                    $isAtrasado = now()->isAfter($requisicao->data_fim_prevista) && $requisicao->status === 'aprovado';
                @endphp
                <tr class="hover">
                    <td class="font-mono text-sm">{{ $requisicao->numero_sequencial }}</td>
                    <td class="font-semibold">{{ $requisicao->livro->nome_visivel ?? 'Livro Removido' }}</td>
                    <td>{{ $requisicao->user->name ?? 'Usuário Removido' }}</td>
                    <td class="text-center">
                        @if ($isAtrasado)
                            <div class="tooltip" data-tip="Atrasado"><span class="text-2xl">🔴</span></div>
                        @elseif ($requisicao->status === 'devolvido')
                            <div class="tooltip" data-tip="Devolvido"><span class="text-2xl">✅</span></div>
                        @elseif ($requisicao->status === 'aprovado')
                            <div class="tooltip" data-tip="Em posse"><span class="text-2xl">🔵</span></div>
                        @elseif ($requisicao->status === 'solicitado')
                            <div class="tooltip" data-tip="Solicitado"><span class="text-2xl">🟡</span></div>
                        @else
                             <div class="tooltip" data-tip="Cancelado"><span class="text-2xl">⚪</span></div>
                        @endif
                    </td>
                    <td>{{ optional($requisicao->data_inicio)->format('d/m/Y') }}</td>
                    <td>{{ optional($requisicao->data_fim_prevista)->format('d/m/Y') }}</td>
                    <td>
                        <!-- ============================================= -->
                        <!--      LÓGICA DE AÇÕES FINAL E CORRIGIDA        -->
                        <!-- ============================================= -->
                        <div class="flex items-center gap-2">

                            {{-- Ação para status SOLICITADO --}}
                            @if ($requisicao->status === 'solicitado')
                                <form action="{{ route('requisicoes.aprovar', $requisicao) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success" title="Aprovar">✅ Aprovar</button>
                                </form>
                                <form action="{{ route('requisicoes.cancelar', $requisicao) }}" method="POST" onsubmit="return confirm('Tem a certeza que deseja DELETAR esta requisição?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-error btn-outline" title="Deletar Requisição">🗑️</button>
                                </form>
                            @endif

                            {{-- Ação para status APROVADO --}}
                            @if ($requisicao->status === 'aprovado')
                                <button class="btn btn-sm btn-info" onclick="devolucao_modal_{{ $requisicao->id }}.showModal()">
                                    📚 Registrar Devolução
                                </button>
                                <dialog id="devolucao_modal_{{ $requisicao->id }}" class="modal">
                                    <div class="modal-box">
                                        <h3 class="font-bold text-lg">Registrar Devolução</h3>
                                        <p class="py-2">Livro: <span class="font-semibold">{{ $requisicao->livro->nome_visivel ?? 'N/A' }}</span></p>
                                        <form method="POST" action="{{ route('requisicoes.entregar', $requisicao) }}" class="mt-4 space-y-4">
                                            @csrf @method('PATCH')
                                            <div>
                                                <label class="label"><span class="label-text">Data de Devolução Real</span></label>
                                                <input type="date" name="data_fim_real" class="input input-bordered w-full" value="{{ now()->format('Y-m-d') }}" required>
                                            </div>
                                            <div>
                                                <label class="label"><span class="label-text">Observações</span></label>
                                                <textarea name="observacoes" class="textarea textarea-bordered w-full" placeholder="Opcional..."></textarea>
                                            </div>
                                            <div class="modal-action">
                                                <form method="dialog"><button class="btn">Cancelar</button></form>
                                                <button type="submit" class="btn btn-primary">Confirmar</button>
                                            </div>
                                        </form>
                                    </div>
                                    <form method="dialog" class="modal-backdrop"><button>close</button></form>
                                </dialog>
                            @endif

                            {{-- Se já foi devolvido, não mostra ações --}}
                            @if ($requisicao->status === 'devolvido')
                                <span>-</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center py-10"><p>Nenhuma requisição encontrada com os filtros atuais.</p></td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $requisicoes->links() }}
</div>