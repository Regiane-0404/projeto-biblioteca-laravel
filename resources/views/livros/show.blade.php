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
                                $imageUrl = null;
                                if ($livro->imagem_capa) {
                                    if (str_starts_with($livro->imagem_capa, 'http')) {
                                        $imageUrl = $livro->imagem_capa;
                                    } elseif (Storage::disk('public')->exists($livro->imagem_capa)) {
                                        $imageUrl = asset('storage/' . $livro->imagem_capa);
                                    }
                                }
                            @endphp

                            @if ($imageUrl)
                                <img src="{{ $imageUrl }}" alt="Capa de {{ $livro->nome }}"
                                    class="rounded-xl w-full max-w-sm h-auto shadow-lg" />
                            @else
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
                                        <td class="font-semibold">{{ $requisicao->user->name ?? 'Usuário Removido' }}</td>
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

            {{-- ... fim do card do Histórico de Requisições ... --}}

            <!-- ============================================= -->
            <!--   NOVO CARD PARA MOSTRAR AS AVALIAÇÕES        -->
            <!-- ============================================= -->
            <div class="card bg-base-100 shadow-xl mt-8">
                <div class="card-body">
                    <h3 class="card-title text-2xl mb-4">⭐ Opiniões dos Leitores</h3>

                    @forelse ($livro->reviews as $review)
                        <div class="chat chat-start">
                            <div class="chat-image avatar">
                                <div class="w-10 rounded-full">
                                    <img alt="Avatar de {{ $review->user->name }}"
                                        src="{{ $review->user->profile_photo_url }}" />
                                </div>
                            </div>
                            <div class="chat-header">
                                {{ $review->user->name }}
                                <time class="text-xs opacity-50 ml-2">{{ $review->created_at->format('d/m/Y') }}</time>
                            </div>
                            <div class="chat-bubble">
                                @if ($review->comentario)
                                    {{ $review->comentario }}
                                @else
                                    <span class="italic">Este usuário não deixou um comentário.</span>
                                @endif
                            </div>
                            <div class="chat-footer">
                                <div class="rating rating-sm">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <input type="radio" name="rating-{{ $review->id }}"
                                            class="mask mask-star-2 bg-orange-400"
                                            {{ $i == $review->classificacao ? 'checked' : '' }} disabled />
                                    @endfor
                                </div>
                            </div>
                        </div>
                        @if (!$loop->last)
                            <div class="divider"></div>
                        @endif
                    @empty
                        <div class="text-center py-6 text-gray-500">
                            <p>Este livro ainda não tem nenhuma avaliação pública. Seja o primeiro a opinar!</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ... fim do card das Opiniões dos Leitores ... --}}

            <!-- ============================================= -->
            <!--   NOVA SECÇÃO DE LIVROS RELACIONADOS          -->
            <!-- ============================================= -->
            @if ($livrosRelacionados->isNotEmpty())
                <div class="mt-8">
                    <h3 class="text-2xl font-bold mb-4">Você também pode gostar de...</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach ($livrosRelacionados as $relacionado)
                            <a href="{{ route('livros.show', $relacionado) }}" class="card bg-base-100 shadow-xl transition-transform hover:scale-105">
                                <figure class="px-4 pt-4 h-56">
                                    @php
                                        $imageUrl = null;
                                        if ($relacionado->imagem_capa) {
                                            if (str_starts_with($relacionado->imagem_capa, 'http')) $imageUrl = $relacionado->imagem_capa;
                                            elseif (Storage::disk('public')->exists($relacionado->imagem_capa)) $imageUrl = asset('storage/' . $relacionado->imagem_capa);
                                        }
                                    @endphp
                                    @if($imageUrl)
                                        <img src="{{ $imageUrl }}" alt="Capa de {{ $relacionado->nome_visivel }}" class="rounded-lg object-contain h-full w-full" />
                                    @else
                                        <div class="w-full h-full bg-base-200 rounded-lg flex items-center justify-center"><span class="text-4xl opacity-30">📚</span></div>
                                    @endif
                                </figure>
                                <div class="card-body p-4 items-center text-center">
                                    <h2 class="card-title text-sm h-10">{{ Str::limit($relacionado->nome_visivel, 35) }}</h2>
                                    <div class="card-actions">
                                        @if($relacionado->quantidade > 0)
                                            <div class="badge badge-success badge-outline">Disponível</div>
                                        @else
                                            <div class="badge badge-error badge-outline">Esgotado</div>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Botão Voltar -->
            <div class="mt-8 flex justify-center">
                <a href="{{ route('livros.index') }}" class="btn btn-outline btn-primary">
                    ⬅️ Voltar para a Lista de Livros
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
