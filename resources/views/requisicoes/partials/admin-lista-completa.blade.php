<!-- Card de Filtros (Visual neutro e profissional) -->
<div class="card bg-base-200 shadow-md mb-6">
    <div class="card-body">
        <form method="GET" action="{{ route('requisicoes.index') }}">
            <div class="flex flex-wrap items-end gap-4">

                <!-- Filtro Data De -->
                <div class="form-control">
                    <label class="label"><span class="label-text">Desde:</span></label>
                    <input type="date" name="data_de" value="{{ $filtro_data_de ?? '' }}" class="input input-bordered">
                </div>

                <!-- Filtro Data Até -->
                <div class="form-control">
                    <label class="label"><span class="label-text">Até:</span></label>
                    <input type="date" name="data_ate" value="{{ $filtro_data_ate ?? '' }}"
                        class="input input-bordered">
                </div>

                <!-- Botão Pesquisar -->
                <div class="form-control">
                    <label class="label opacity-0">.</label>
                    <button type="submit" class="btn btn-outline" title="Filtrar por Data">Pesquisar</button>
                </div>

                <!-- Filtro por Status -->
                <div class="form-control min-w-[200px] ml-auto">
                    <label class="label"><span class="label-text">Status:</span></label>
                    <select name="status" class="select select-bordered w-full" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        <option value="solicitado" @if (request('status') == 'solicitado') selected @endif>Solicitado</option>
                        <option value="aprovado" @if (request('status') == 'aprovado') selected @endif>Em posse</option>
                        <option value="devolvido" @if (request('status') == 'devolvido') selected @endif>Devolvido</option>
                    </select>
                </div>

                <!-- Botão Limpar Filtros -->
                <div class="form-control">
                    <label class="label opacity-0">.</label>
                    <a href="{{ route('requisicoes.index', ['tab' => 'lista']) }}" class="btn btn-outline btn-neutral"
                        title="Limpar Filtros">Limpar Filtros</a>
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
                        <div class="flex items-center gap-2">

                            {{-- Botão Aprovar --}}
                            @if ($requisicao->status === 'solicitado')
                                <form action="{{ route('requisicoes.aprovar', $requisicao) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline btn-success"
                                        title="Aprovar">Aprovar</button>
                                </form>
                            @endif

                            {{-- Botão Registrar Devolução --}}
                            @if ($requisicao->status === 'aprovado')
                                <button class="btn btn-sm btn-outline btn-info"
                                    onclick="devolucao_modal_{{ $requisicao->id }}.showModal()">
                                    Registrar Devolução
                                </button>
                                <dialog id="devolucao_modal_{{ $requisicao->id }}" class="modal">
                                    <div class="modal-box">
                                        <h3 class="font-bold text-lg">Registrar Devolução</h3>
                                        <p class="py-2 text-sm">Livro: <span
                                                class="font-semibold">{{ $requisicao->livro->nome_visivel ?? 'N/A' }}</span>
                                        </p>
                                        <form method="POST" action="{{ route('requisicoes.entregar', $requisicao) }}"
                                            class="mt-4 space-y-4">
                                            @csrf @method('PATCH')
                                            <div>
                                                <label class="label"><span class="label-text">Data de Devolução
                                                        Real</span></label>
                                                <input type="date" name="data_fim_real"
                                                    class="input input-bordered w-full"
                                                    value="{{ now()->format('Y-m-d') }}" required>
                                            </div>

                                            <!-- NOVO CAMPO ESTADO DO LIVRO -->
                                            <div>
                                                <label class="label">
                                                    <span class="label-text">Estado do Livro na Devolução</span>
                                                </label>
                                                <select name="estado_devolucao" class="select select-bordered w-full"
                                                    required>
                                                    <option value="intacto">Intacto (Perfeito estado)</option>
                                                    <option value="marcas_uso">Com pequenas marcas de uso</option>
                                                    <option value="danificado">Danificado (Ex: rasgado, molhado)
                                                    </option>
                                                    <option value="nao_devolvido">Não foi devolvido</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label class="label"><span class="label-text">Observações
                                                        (opcional)</span></label>
                                                <textarea name="observacoes" class="textarea textarea-bordered w-full"
                                                    placeholder="Ex: Livro devolvido com uma pequena marca na capa..."></textarea>
                                            </div>
                                            <div class="modal-action">
                                                <form method="dialog"><button class="btn btn-ghost">Cancelar</button>
                                                </form>
                                                <button type="submit" class="btn btn-primary">Confirmar
                                                    Devolução</button>
                                            </div>
                                        </form>
                                    </div>
                                    <form method="dialog" class="modal-backdrop"><button>Fechar</button></form>
                                </dialog>
                            @endif

                            {{-- Botão Eliminar --}}
                            @if (in_array($requisicao->status, ['solicitado', 'aprovado']))
                                <button class="btn btn-sm btn-outline btn-error" aria-label="Eliminar Requisição"
                                    onclick="eliminar_modal_{{ $requisicao->id }}.showModal()">
                                    🗑️
                                </button>
                                <dialog id="eliminar_modal_{{ $requisicao->id }}" class="modal">
                                    <div class="modal-box">
                                        <h3 class="font-bold text-lg">Eliminar Requisição</h3>
                                        <p class="py-2">Tem a certeza que deseja eliminar esta requisição?</p>
                                        <form method="POST"
                                            action="{{ route('requisicoes.cancelar', $requisicao) }}" class="mt-4">
                                            @csrf @method('DELETE')
                                            <div class="modal-action">
                                                <form method="dialog"><button class="btn btn-ghost">Cancelar</button>
                                                </form>
                                                <button type="submit" class="btn btn-error">Eliminar</button>
                                            </div>
                                        </form>
                                    </div>
                                    <form method="dialog" class="modal-backdrop"><button>Fechar</button></form>
                                </dialog>
                            @endif

                            {{-- Status Devolvido --}}
                            @if ($requisicao->status === 'devolvido')
                                <span>-</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-10">
                        <p>Nenhuma requisição encontrada com os filtros atuais.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $requisicoes->links() }}
</div>
