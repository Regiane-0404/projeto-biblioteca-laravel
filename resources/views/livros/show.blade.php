<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-base-content">
                📖 Detalhes: {{ $livro->nome }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Grid Principal: Capa e Informações -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Coluna da Capa -->
                <div class="lg:col-span-1">
                    <div class="card bg-base-100 shadow-xl">
                        <figure class="px-6 pt-6">

                            @php
                                // Esta pequena lógica verifica se a imagem da capa é um URL (começa com http)
                                $imageUrl = null;
                                if ($livro->imagem_capa) {
                                    if (str_starts_with($livro->imagem_capa, 'http')) {
                                        $imageUrl = $livro->imagem_capa; // É um URL, usamos diretamente
                                    } elseif (Storage::disk('public')->exists($livro->imagem_capa)) {
                                        $imageUrl = asset('storage/' . $livro->imagem_capa); // É um ficheiro local, criamos o link
                                    }
                                }
                            @endphp

                            @if ($imageUrl)
                                <img src="{{ $imageUrl }}" alt="Capa de {{ $livro->nome }}"
                                    class="rounded-xl w-full max-w-sm h-auto shadow-lg" />
                            @else
                                {{-- Código para quando não há capa --}}
                                <div
                                    class="w-full max-w-sm h-96 bg-gradient-to-br from-base-300 to-base-200 rounded-xl flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="text-6xl opacity-30 mb-4">📚</div>
                                        <p class="text-base-content/60">Sem capa disponível</p>
                                    </div>
                                </div>
                            @endif

                        </figure>

                        <div class="card-body text-center">
                            <div class="text-3xl font-bold text-success">
                                €{{ number_format($livro->preco, 2, ',', '.') }}
                            </div>
                            <p class="text-sm text-base-content/60">Preço de venda</p>
                        </div>
                    </div>
                </div>

                <!-- Coluna das Informações -->
                <div class="lg:col-span-2">
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <h1 class="text-3xl font-bold text-base-content mb-3">{{ $livro->nome }}</h1>
                            <p class="text-base-content/70 text-lg"><strong>ISBN:</strong> {{ $livro->isbn }}</p>
                            <div class="space-y-6 mt-6">
                                <div>
                                    <h3 class="font-semibold text-base-content text-lg mb-2">🏢 Editora</h3>
                                    <p class="text-base-content/80 text-lg">
                                        {{ $livro->editora->nome ?? 'Não informado' }}</p>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-base-content text-lg mb-2">✍️
                                        {{ Str::plural('Autor', $livro->autores->count()) }}</h3>
                                    <p class="text-base-content/80 text-lg">
                                        {{ $livro->autores->pluck('nome')->join(', ') ?: 'Nenhum autor associado' }}</p>
                                </div>
                                @if ($livro->bibliografia)
                                    <div>
                                        <h3 class="font-semibold text-base-content text-lg mb-3">📄 Sobre o livro</h3>
                                        <div class="bg-base-200/50 rounded-lg p-4 border-l-4 border-primary">
                                            <p class="text-base-content leading-relaxed">{{ $livro->bibliografia }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card com Histórico de Requisições -->
            <div class="card bg-base-100 shadow-xl mt-8">
                <div class="card-body">
                    <h3 class="card-title text-2xl mb-4">📜 Histórico de Requisições</h3>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>Cidadão</th>
                                    <th>Data da Requisição</th>
                                    <th>Data de Devolução (Prevista)</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($livro->requisicoes as $requisicao)
                                    <tr>
                                        <td class="font-semibold">{{ $requisicao->user->name ?? 'Usuário Removido' }}
                                        </td>
                                        <td>{{ optional($requisicao->data_inicio)->format('d/m/Y') }}</td>
                                        <td>{{ optional($requisicao->data_fim_prevista)->format('d/m/Y') }}</td>
                                        <td>
                                            @if ($requisicao->status === 'solicitado')
                                                <span class="badge badge-warning">🟡 Solicitado</span>
                                            @elseif ($requisicao->status === 'aprovado')
                                                <span class="badge badge-info">🔵 Aprovado</span>
                                            @elseif (in_array($requisicao->status, ['entregue', 'devolvido']))
                                                <span class="badge badge-success">✅ Concluído</span>
                                            @elseif ($requisicao->status === 'cancelado')
                                                <span class="badge badge-error">❌ Cancelado</span>
                                            @else
                                                <span class="badge">{{ $requisicao->status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-8">
                                            <div class="text-gray-500">Este livro nunca foi requisitado.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ============================================= -->
            <!--   BOTÃO VOLTAR NO FINAL DA PÁGINA             -->
            <!-- ============================================= -->
            <div class="mt-8 flex justify-center">
                <a href="{{ route('livros.index') }}" class="btn btn-outline btn-primary">
                    ⬅️ Voltar para a Lista de Livros
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
