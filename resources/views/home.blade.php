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

            <!-- Secção de Boas-vindas e Pesquisa (VERSÃO CORRIGIDA) -->
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

            <!-- Grelha de Livros (VERSÃO CORRIGIDA) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse ($livros as $livro)
                    <div class="card bg-base-100 shadow-xl transition-transform hover:scale-105">
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
                                        ({{ $livro->quantidade }})</div>
                                @else
                                    <div class="badge badge-error badge-outline">Esgotado</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="col-span-full text-center py-10 text-xl text-base-content/50">Nenhum livro encontrado com
                        os filtros atuais.</p>
                @endforelse
            </div>

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
