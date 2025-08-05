<nav x-data="{ open: false }" class="navbar bg-base-100 shadow-lg">
    <!-- Secção Esquerda: Hamburger (Mobile) e Nome da App -->
    <div class="navbar-start">
        <div class="dropdown lg:hidden">
            <div tabindex="0" role="button" class="btn btn-ghost">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                </svg>
            </div>
            <!-- Menu Mobile -->
            <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                @auth
                    <li><a href="{{ route('dashboard') }}">🏠 Dashboard</a></li>
                    <li><a>📖 Livros</a>
                        <ul class="p-2">
                            <li><a href="{{ route('livros.index') }}">Ver Lista</a></li>
                            @if (auth()->user()->role === 'admin')
                                <li><a href="{{ route('livros.create') }}">Inserir Manual</a></li>
                                <li><a href="{{ route('livros.importar.form') }}">Importar</a></li>
                            @endif
                        </ul>
                    </li>
                    @if (auth()->user()->role === 'admin')
                        <li><a href="{{ route('autores.index') }}">✍️ Autores</a></li>
                        <li><a href="{{ route('editoras.index') }}">🏢 Editoras</a></li>
                    @endif
                    <li><a>📋 Requisições</a>
                        <ul class="p-2">
                            <li><a href="{{ route('requisicoes.index') }}">📋
                                    {{ auth()->user()->role === 'admin' ? 'Ver Todas' : 'Minhas' }}</a></li>
                            <li><a href="{{ route('requisicoes.create') }}">➕ Nova</a></li>
                        </ul>
                    </li>
                    @if (auth()->user()->role === 'admin')
                        <li><a>👥 Gestão de Utilizadores</a>
                            <ul class="p-2">
                                <li><a href="{{ route('users.index') }}">Ver Lista</a></li>
                                <li><a href="{{ route('users.create') }}">➕ Adicionar Novo</a></li>
                                <div class="divider my-1"></div>
                                <li><a href="{{ route('admin.reviews.index') }}">⭐ Moderar Avaliações</a></li>
                            </ul>
                        </li>
                    @endif
                @else
                    {{-- Links para visitantes no menu mobile, se necessário --}}
                    <li><a href="{{ route('home') }}">🏠 Home</a></li>
                @endauth
            </ul>
        </div>
        <a href="{{ route('home') }}" class="btn btn-ghost text-xl normal-case">📚 {{ config('app.name', 'Biblioteca') }}</a>
    </div>

    <!-- Secção Central: Menu Principal para Desktop -->
    <div class="navbar-center hidden lg:flex">
        <ul class="menu menu-horizontal px-1">
            @auth
                <li><a href="{{ route('dashboard') }}"
                        class="text-base {{ request()->routeIs('dashboard') ? 'active' : '' }}">🏠 Dashboard</a></li>
                <li>
                    <details>
                        <summary class="text-base">📖 Livros</summary>
                        <ul class="p-2 bg-base-100 rounded-t-none z-[1]">
                            <li><a href="{{ route('livros.index') }}">📖 Ver Lista</a></li>
                            @if (auth()->user()->role === 'admin')
                                <li><a href="{{ route('livros.create') }}">➕ Inserir (Manual)</a></li>
                                <li><a href="{{ route('livros.importar.form') }}">🔎 Importar (Google)</a></li>
                            @endif
                        </ul>
                    </details>
                </li>
                @if (auth()->user()->role === 'admin')
                    <li><a href="{{ route('autores.index') }}" class="text-base">✍️ Autores</a></li>
                    <li><a href="{{ route('editoras.index') }}" class="text-base">🏢 Editoras</a></li>
                @endif
                <li>
                    <details>
                        <summary class="text-base">📋 Requisições</summary>
                        <ul class="p-2 bg-base-100 rounded-t-none z-[1]">
                            <li><a href="{{ route('requisicoes.index') }}">📋
                                    {{ auth()->user()->role === 'admin' ? 'Ver Todas' : 'Minhas Requisições' }}</a></li>
                            <li><a href="{{ route('requisicoes.create') }}">➕ Nova Requisição</a></li>
                        </ul>
                    </details>
                </li>
                @if (Auth::user()->role === 'admin')
                    <li>
                        <details>
                            <summary>👥 Gestão de Utilizadores</summary>
                            <ul class="p-2 bg-base-100 rounded-t-none z-[1]">
                                <li>
                                    <a href="{{ route('users.index') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Ver Lista de Utilizadores
                                    </a>
                                </li>
                                <li><a href="{{ route('users.create') }}">➕ Adicionar Novo</a></li>
                                <div class="divider my-1"></div>
                                <li><a href="{{ route('admin.reviews.index') }}">⭐ Moderar Avaliações</a></li>
                            </ul>
                        </details>
                    </li>
                @endif
            @endauth
        </ul>
    </div>

    <!-- Secção Direita: Ícone do Carrinho + Dropdown do Perfil -->
    <div class="navbar-end">
        <a href="{{ route('cart.index') }}" class="btn btn-ghost btn-circle">
            <div class="indicator">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                @if (isset($cartCount) && $cartCount > 0)
                    <span class="badge badge-sm indicator-item">{{ $cartCount }}</span>
                @endif
            </div>
        </a>

        @auth
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                    <div class="w-10 rounded-full">
                        <img alt="{{ Auth::user()->name }}" src="{{ Auth::user()->profile_photo_url }}" />
                    </div>
                </div>
                <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                    <li class="menu-title"><span>{{ Auth::user()->name }}</span></li>
                    <li><a href="{{ route('profile.show') }}">⚙️ Perfil</a></li>
                    <div class="divider my-1"></div>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" x-data>
                            @csrf
                            <a href="{{ route('logout') }}" @click.prevent="$root.submit();">🚪 Sair</a>
                        </form>
                    </li>
                </ul>
            </div>
        @else
            <a href="{{ route('login') }}" class="btn btn-sm btn-ghost">Login</a>
            <a href="{{ route('register') }}" class="btn btn-sm">Registar</a>
        @endauth
    </div>
</nav>