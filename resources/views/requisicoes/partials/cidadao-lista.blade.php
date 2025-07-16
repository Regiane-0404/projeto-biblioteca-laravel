<!-- Ficheiro: resources/views/requisicoes/partials/cidadao-lista.blade.php -->

<div class="overflow-x-auto">
    <table class="table w-full">
        <thead>
            <tr>
                <th>Número</th>
                <th>Livro</th>
                <th>Status</th>
                <th>Data Início</th>
                <th>Data Fim (Prevista)</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requisicoes as $requisicao)
                <tr class="hover">
                    <td class="font-mono text-sm">{{ $requisicao->numero_sequencial }}</td>
                    <td class="font-semibold">{{ $requisicao->livro->nome_visivel ?? 'Livro Removido' }}</td>
                    <td>
                        @if ($requisicao->status === 'solicitado')
                            <span class="badge badge-warning">Solicitado</span>
                        @elseif ($requisicao->status === 'aprovado')
                            <span class="badge badge-info">Em posse</span>
                        @elseif ($requisicao->status === 'devolvido')
                            <span class="badge badge-success">Devolvido</span>
                        @else
                            <span class="badge badge-ghost">{{ ucfirst($requisicao->status) }}</span>
                        @endif
                    </td>
                    <td>{{ optional($requisicao->data_inicio)->format('d/m/Y') }}</td>
                    <td>{{ optional($requisicao->data_fim_prevista)->format('d/m/Y') }}</td>
                    <td>
                        {{-- O Cidadão só pode cancelar se o status for 'solicitado' --}}
                        @if ($requisicao->status === 'solicitado')
                            <form action="{{ route('requisicoes.cancelar', $requisicao) }}" method="POST" onsubmit="return confirm('Tem a certeza que deseja cancelar o seu pedido?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline btn-error">Cancelar Pedido</button>
                            </form>
                        @else
                            <span>-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-10">
                        <p class="text-base-content/70">Você ainda não fez nenhuma requisição.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Paginação -->
<div class="mt-6">
    {{ $requisicoes->links() }}
</div>