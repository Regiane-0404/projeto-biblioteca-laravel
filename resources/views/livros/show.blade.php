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

                            <!-- Bloco de Ações Dinâmicas -->
                            <div class="mt-6 space-y-4">

                                {{-- ======================= BOTÃO DE EMPRÉSTIMO ======================= --}}
                                @if ($livro->quantidade > 0)
                                    {{-- Verifica se o utilizador pode requisitar --}}
                                    @if (auth()->check() && in_array(auth()->user()->role, ['cidadao', 'admin']))
                                        <a href="{{ route('requisicoes.create', ['livro_id' => $livro->id]) }}"
                                            class="btn btn-secondary w-full shadow-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            Requisitar Empréstimo ({{ $livro->quantidade }} disponíveis)
                                        </a>
                                    @endif
                                @else
                                    {{-- Lógica de "Avise-me" para empréstimo --}}
                                    @if (auth()->check())
                                        @php
                                            $jaPediuAlerta = \App\Models\AlertaDisponibilidade::where(
                                                'user_id',
                                                auth()->id(),
                                            )
                                                ->where('livro_id', $livro->id)
                                                ->exists();
                                        @endphp
                                        @if ($jaPediuAlerta)
                                            <div class="alert alert-info shadow-sm">
                                                <div>
                                                    <span>👍 Já está na lista de espera para empréstimo.</span>
                                                </div>
                                            </div>
                                        @else
                                            <form method="POST"
                                                action="{{ route('livros.solicitar-alerta', $livro) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-outline btn-secondary w-full">
                                                    🔔 Avise-me quando houver para empréstimo
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                @endif

                                {{-- ======================= BOTÃO DE COMPRA (CARRINHO) ======================= --}}
                                {{-- @if ($livro->preco && $livro->preco > 0)
                                    @if ($livro->quantidade_venda > 0)
                                        <form action="{{ route('cart.add', $livro->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="btn btn-primary w-full shadow-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                                Comprar por €{{ number_format((float)$livro->preco, 2, ',', '.') }}
                                                <div class="badge badge-outline ml-2">{{ $livro->quantidade_venda }} em stock</div>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-primary w-full" disabled>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                            Esgotado para Venda
                                        </button>
                                    @endif
                                @endif --}}
                                {{-- ======================= BOTÃO DE COMPRA (CARRINHO) ======================= --}}
                                {{-- A correção está em (float)$livro->preco > 0 --}}
                                @if ((float) $livro->preco > 0 && $livro->quantidade_venda > 0)
                                    <form action="{{ route('cart.add', $livro->id) }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-primary w-full shadow-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            Comprar por €{{ number_format((float) $livro->preco, 2, ',', '.') }}
                                            <div class="badge badge-outline ml-2">{{ $livro->quantidade_venda }} em
                                                stock</div>
                                        </button>
                                    </form>
                                @elseif((float) $livro->preco > 0)
                                    {{-- Mostra 'Esgotado' se tiver preço mas não tiver stock de venda --}}
                                    <button class="btn btn-primary w-full" disabled>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        Esgotado para Venda
                                    </button>
                                @endif
                            </div>

                            <div class="divider"></div>

                            <div class="space-y-6 mt-2">
                                <div>
                                    <h3 class="font-semibold text-base-content text-lg mb-2">🏢 Editora</h3>
                                    <p class="text-base-content/80 text-lg">
                                        {{ $livro->editora->nome ?? 'Não informado' }}</p>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-base-content text-lg mb-2">✍️
                                        {{ Str::plural('Autor', $livro->autores->count()) }}</h3>
                                    <p class="text-base-content/80 text-lg">
                                        {{ $livro->autores->pluck('nome')->join(', ') ?: 'Nenhum autor associado' }}
                                    </p>
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

            <!-- Histórico de Requisições -->
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

            <!-- Livros Relacionados -->
            @if ($livrosRelacionados->isNotEmpty())
                <div class="mt-8">
                    <h3 class="text-2xl font-bold mb-4">Você também pode gostar de...</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach ($livrosRelacionados as $relacionado)
                            <a href="{{ route('livros.show', $relacionado) }}"
                                class="card bg-base-100 shadow-xl transition-transform hover:scale-105">
                                <figure class="px-4 pt-4 h-56">
                                    @php
                                        $imageUrl = null;
                                        if ($relacionado->imagem_capa) {
                                            if (str_starts_with($relacionado->imagem_capa, 'http')) {
                                                $imageUrl = $relacionado->imagem_capa;
                                            } elseif (Storage::disk('public')->exists($relacionado->imagem_capa)) {
                                                $imageUrl = asset('storage/' . $relacionado->imagem_capa);
                                            }
                                        }
                                    @endphp
                                    @if ($imageUrl)
                                        <img src="{{ $imageUrl }}" alt="Capa de {{ $relacionado->nome_visivel }}"
                                            class="rounded-lg object-contain h-full w-full" />
                                    @else
                                        <div
                                            class="w-full h-full bg-base-200 rounded-lg flex items-center justify-center">
                                            <span class="text-4xl opacity-30">📚</span>
                                        </div>
                                    @endif
                                </figure>
                                <div class="card-body p-4 items-center text-center">
                                    <h2 class="card-title text-sm h-10">
                                        {{ Str::limit($relacionado->nome_visivel, 35) }}</h2>
                                    <div class="card-actions">
                                        @if ($relacionado->quantidade > 0)
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
