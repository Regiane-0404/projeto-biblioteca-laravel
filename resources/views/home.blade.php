<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-t">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Bem-vindo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-base-200">

    <!-- Cabeçalho Público -->
    <header class="bg-base-100 shadow">
        <div class="navbar max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="navbar-start">
                <a href="{{ route('home') }}" class="btn btn-ghost text-xl normal-case">
                    📚 {{ config('app.name', 'Biblioteca') }}
                </a>
            </div>
            <div class="navbar-end">
                @if (Route::has('login'))
                    <div class="space-x-2">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline">Meu Painel</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-sm btn-outline">Login</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-sm btn-primary">Registar</a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </header>

    <!-- Conteúdo Principal -->
    <main class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Secção de Boas-vindas e Pesquisa -->
            <div class="bg-base-100 p-6 rounded-lg shadow-lg mb-8">
                <h1 class="text-3xl font-bold mb-4 text-center">Bem-vindo ao nosso Acervo!</h1>
                <form method="GET" action="{{ route('home') }}"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div class="form-control md:col-span-2">
                        <label class="label"><span class="label-text">Pesquisar por Título ou Autor</span></label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Digite para pesquisar..." class="input input-bordered w-full">
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text">Filtrar por Editora</span></label>
                        <select name="editora" class="select select-bordered w-full" onchange="this.form.submit()">
                            <option value="">Todas as Editoras</option>
                            @foreach ($editoras as $editora)
                                <option value="{{ $editora->id }}"
                                    {{ request('editora') == $editora->id ? 'selected' : '' }}>{{ $editora->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow">Filtrar</button>
                        <a href="{{ route('home') }}" class="btn btn-ghost">Limpar</a>
                    </div>
                </form>
            </div>

            <!-- Grelha de Livros com Modais -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse ($livros as $livro)
                    <div class="card bg-base-100 shadow-xl transition-transform hover:scale-105 cursor-pointer"
                        onclick="modal_livro_{{ $livro->id }}.showModal()">
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
                            <div class="card-actions justify-end mt-2">
                                @if ($livro->quantidade > 0)
                                    <div class="badge badge-success badge-outline">Disponível
                                        ({{ $livro->quantidade }})
                                    </div>
                                @else
                                    <div class="badge badge-error badge-outline">Esgotado</div>
                                @endif
                            </div>
                        </div>
                    </div>

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
                                        {{ $livro->bibliografia ?: 'Sem sinopse disponível.' }}
                                    </p>
                                    <div class="alert alert-info mt-6">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            class="stroke-current shrink-0 w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <h3 class="font-bold">Gostou?</h3>
                                            <div class="text-xs">Para requisitar este livro, faça o seu registo ou entre
                                                na sua conta!</div>
                                        </div>
                                        <div class="space-x-2">
                                            <a href="{{ route('login') }}" class="btn btn-sm">Login</a>
                                            <a href="{{ route('register') }}"
                                                class="btn btn-sm btn-primary">Registar</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-action">
                                <form method="dialog">
                                    <button class="btn">Fechar</button>
                                </form>
                            </div>
                        </div>
                    </dialog>
                @empty
                    <p class="col-span-full text-center py-10 text-xl text-base-content/50">Nenhum livro encontrado com
                        os filtros atuais.</p>
                @endforelse
            </div>

            {{-- ======================================================= --}}
            {{-- == CARROSSEL DE AVALIAÇÕES (NOVO DESIGN)             == --}}
            {{-- ======================================================= --}}
            @if ($reviewsRecentes->isNotEmpty())
                <div class="mt-16">
                    <h2 class="text-3xl font-bold mb-6 text-center">⭐ A Opinião dos Nossos Leitores</h2>
                    <div class="carousel w-full bg-base-100 rounded-box shadow-lg">
                        @foreach ($reviewsRecentes as $index => $review)
                            <div id="slide{{ $review->id }}" class="carousel-item relative w-full">

                                <div
                                    class="flex flex-col md:flex-row items-center justify-center gap-6 md:gap-12 p-8 w-full">
                                    <!-- Coluna da Capa do Livro -->
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
                                                    <span class="text-4xl opacity-30">📚</span></div>
                                            @endif
                                        </a>
                                    </div>

                                    <!-- Coluna da Avaliação -->
                                    <div class="text-center md:text-left max-w-md">
                                        <div class="rating rating-sm">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <input type="radio" name="rating-slide-{{ $review->id }}"
                                                    class="mask mask-star-2 bg-orange-400"
                                                    @if ($i == $review->classificacao) checked @endif disabled />
                                            @endfor
                                        </div>
                                        <p class="text-lg italic mt-2">
                                            "{{ Str::limit($review->comentario, 150) }}"
                                        </p>
                                        <div class="mt-4">
                                            <div class="font-semibold">{{ $review->user->name ?? 'Leitor Anónimo' }}
                                            </div>
                                            {{-- AQUI VAI ENTRAR O GÉNERO QUANDO O CRIARMOS --}}
                                            {{-- <div class="badge badge-ghost mt-1">Ficção Científica</div> --}}
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

            <!-- Paginação -->
            <div class="mt-8">
                {{ $livros->links() }}
            </div>

        </div>
    </main>

    <!-- Rodapé -->
    <footer class="footer footer-center p-4 bg-base-300 text-base-content mt-12">
        <aside>
            <p>© {{ date('Y') }} - {{ config('app.name') }} - Todos os direitos reservados.</p>
        </aside>
    </footer>

</body>

</html>
