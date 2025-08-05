<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Bem-vindo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-base-200">

    <!-- Cabeçalho Público -->
    <header class="bg-base-100 shadow">
        @livewire('navigation-menu')
    </header>

    <!-- Conteúdo Principal -->
    <main class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="alert alert-success shadow-lg mb-6"><span>{{ session('success') }}</span></div>
            @endif
            @if (session('error'))
                <div class="alert alert-error shadow-lg mb-6"><span>{{ session('error') }}</span></div>
            @endif

            <!-- Secção de Pesquisa -->
            <div class="bg-base-100 p-6 rounded-lg shadow-lg mb-8">
                <h1 class="text-3xl font-bold mb-4 text-center">Bem-vindo ao nosso Acervo!</h1>
                <form method="GET" action="{{ route('home') }}"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div class="form-control md:col-span-2"><label class="label"><span class="label-text">Pesquisar por
                                Título ou Autor</span></label><input type="text" name="search"
                            value="{{ request('search') }}" placeholder="Digite para pesquisar..."
                            class="input input-bordered w-full"></div>
                    <div class="form-control"><label class="label"><span class="label-text">Filtrar por
                                Editora</span></label><select name="editora" class="select select-bordered w-full"
                            onchange="this.form.submit()">
                            <option value="">Todas as Editoras</option>
                            @foreach ($editoras as $editora)
                                <option value="{{ $editora->id }}"
                                    {{ request('editora') == $editora->id ? 'selected' : '' }}>{{ $editora->nome }}
                                </option>
                            @endforeach
                        </select></div>
                    <div class="flex gap-2"><button type="submit" class="btn btn-primary flex-grow">Filtrar</button><a
                            href="{{ route('home') }}" class="btn btn-ghost">Limpar</a></div>
                </form>
            </div>

            <!-- Grelha de Livros com Modais -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse ($livros as $livro)
                    <div class="card bg-base-100 shadow-xl transition-transform hover:scale-105 cursor-pointer"
                        onclick="document.getElementById('modal_livro_{{ $livro->id }}').showModal()">
                        <figure class="px-4 pt-4 h-64">
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
                                    class="rounded-lg object-contain h-full w-full" />
                            @else
                                <div class="w-full h-full bg-base-200 rounded-lg flex items-center justify-center"><span
                                        class="text-4xl opacity-30">📚</span></div>
                            @endif
                        </figure>
                        <div class="card-body p-4 items-center text-center">
                            <h2 class="card-title text-base h-12">{{ Str::limit($livro->nome, 45) }}</h2>
                            <p class="text-xs text-base-content/70 h-8">
                                {{ $livro->autores->pluck('nome')->join(', ') }}</p>

                            {{-- ============================================= --}}
                            {{-- == INÍCIO DO BLOCO DE PREÇO E STATUS CORRIGIDO == --}}
                            {{-- ============================================= --}}
                            @if ($livro->preco_visivel > 0)
                                <p class="font-bold text-primary">
                                    €{{ number_format($livro->preco_visivel, 2, ',', '.') }}</p>
                            @endif

                            <div class="card-actions justify-center mt-2 space-x-1">
                                @if ($livro->quantidade > 0)
                                    <div class="badge badge-success badge-outline">P/ Requisição</div>
                                @endif

                                @if ($livro->preco_visivel > 0 && ($livro->quantidade_venda ?? 0) > 0)
                                    <div class="badge badge-primary badge-outline">À Venda</div>
                                @elseif ($livro->preco_visivel > 0)
                                    <div class="badge badge-outline">Venda Esgotada</div>
                                @endif
                            </div>
                            {{-- ============================================= --}}
                            {{-- == FIM DO BLOCO CORRIGIDO                    == --}}
                            {{-- ============================================= --}}
                        </div>
                    </div>

                    <!-- MODAL PARA CADA LIVRO -->
                    <dialog id="modal_livro_{{ $livro->id }}" class="modal">
                        <div class="modal-box w-11/12 max-w-3xl">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="md:col-span-1">
                                    @if ($imageUrl)
                                        <img src="{{ $imageUrl }}" alt="Capa de {{ $livro->nome }}"
                                            class="rounded-lg shadow-lg w-full" />
                                    @else
                                        <div
                                            class="w-full h-full bg-base-200 rounded-lg flex items-center justify-center">
                                            <span class="text-6xl opacity-30">📚</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="md:col-span-2">
                                    <h3 class="font-bold text-2xl">{{ $livro->nome }}</h3>
                                    <p class="text-sm mt-1">
                                        <strong>Autor(es):</strong>
                                        {{ $livro->autores->pluck('nome')->join(', ') ?: 'Não informado' }}<br>
                                        <strong>Editora:</strong> {{ $livro->editora->nome ?? 'Não informada' }}
                                    </p>
                                    <p class="text-sm font-mono mt-2"><strong>ISBN:</strong>
                                        {{ $livro->isbn ?? 'Não disponível' }}</p>
                                    <div class="divider"></div>
                                    <h4 class="font-semibold mb-2">Sobre o Livro</h4>
                                    <p class="text-base-content/80 text-sm max-h-48 overflow-y-auto">
                                        {{ $livro->bibliografia ?: 'Sem sinopse disponível.' }}</p>

                                    <div class="space-y-4 mt-6">
                                        @if ($livro->preco_visivel > 0 && ($livro->quantidade_venda ?? 0) > 0)
                                            <div class="p-4 bg-primary/10 rounded-lg text-center">
                                                <p class="font-bold text-lg">Compre a sua cópia por
                                                    €{{ number_format($livro->preco_visivel, 2, ',', '.') }}</p>
                                                <form method="POST" action="{{ route('cart.add', $livro) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary mt-2">🛒 Adicionar ao
                                                        Carrinho</button>
                                                </form>
                                            </div>
                                        @endif
                                        {{-- Bloco CORRIGIDO --}}
                                        @if ($livro->quantidade > 0)
                                            <div class="p-4 bg-base-200 rounded-lg text-center">
                                                {{-- Apenas esta frase foi alterada --}}
                                                <p class="font-semibold">Este livro também está disponível para
                                                    requisição gratuita!</p>
                                                <a href="{{ route('login') }}" class="btn btn-outline mt-2">Faça Login
                                                    para Requisitar</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="modal-action">
                                <form method="dialog"><button class="btn">Fechar</button></form>
                            </div>
                        </div>
                    </dialog>
                @empty
                    <p class="col-span-full text-center py-10 text-xl text-base-content/50">Nenhum livro encontrado.</p>
                @endforelse
            </div>

            @if (isset($reviewsRecentes) && $reviewsRecentes->isNotEmpty())
                <div class="mt-16">
                    <h2 class="text-3xl font-bold mb-6 text-center">⭐ A Opinião dos Nossos Leitores</h2>
                    <div class="carousel w-full bg-base-100 rounded-box shadow-lg">
                        @foreach ($reviewsRecentes as $index => $review)
                            <div id="slide{{ $review->id }}" class="carousel-item relative w-full">
                                <div
                                    class="flex flex-col md:flex-row items-center justify-center gap-6 md:gap-12 p-8 w-full">
                                    <div class="w-32 flex-shrink-0">
                                        <a href="{{ route('livros.show', $review->livro) }}">
                                            @php
                                                $reviewImageUrl = null;
                                                if ($review->livro && $review->livro->imagem_capa) {
                                                    if (str_starts_with($review->livro->imagem_capa, 'http')) {
                                                        $reviewImageUrl = $review->livro->imagem_capa;
                                                    } elseif (
                                                        Storage::disk('public')->exists($review->livro->imagem_capa)
                                                    ) {
                                                        $reviewImageUrl = asset(
                                                            'storage/' . $review->livro->imagem_capa,
                                                        );
                                                    }
                                                }
                                            @endphp
                                            @if ($reviewImageUrl)
                                                <img src="{{ $reviewImageUrl }}" class="rounded-lg shadow-lg w-full"
                                                    alt="Capa de {{ $review->livro->nome }}">
                                            @else
                                                <div
                                                    class="w-full h-48 bg-base-200 rounded-lg flex items-center justify-center">
                                                    <span class="text-4xl opacity-30">📚</span>
                                                </div>
                                            @endif
                                        </a>
                                    </div>
                                    <div class="text-center md:text-left max-w-md">
                                        <div class="rating rating-sm">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <input type="radio" name="rating-slide-{{ $review->id }}"
                                                    class="mask mask-star-2 bg-orange-400"
                                                    @if ($i == $review->classificacao) checked @endif disabled />
                                            @endfor
                                        </div>
                                        <p class="text-lg italic mt-2">"{{ Str::limit($review->comentario, 150) }}"
                                        </p>
                                        <div class="mt-4">
                                            <div class="font-semibold">{{ $review->user->name ?? 'Leitor Anónimo' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="absolute flex justify-between transform -translate-y-1/2 left-5 right-5 top-1/2">
                                    <a href="#slide{{ $reviewsRecentes[($index - 1 + $reviewsRecentes->count()) % $reviewsRecentes->count()]->id }}"
                                        class="btn btn-circle btn-ghost">❮</a>
                                    <a href="#slide{{ $reviewsRecentes[($index + 1) % $reviewsRecentes->count()]->id }}"
                                        class="btn btn-circle btn-ghost">❯</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-8">
                {{ $livros->links() }}
            </div>
        </div>
    </main>

    <footer class="footer footer-center p-4 bg-base-300 text-base-content mt-12">
        <aside>
            <p>© {{ date('Y') }} - {{ config('app.name') }} - Todos os direitos reservados.</p>
        </aside>
    </footer>

</body>

</html>
