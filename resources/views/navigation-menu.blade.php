<nav x-data="{ open: false }" class="navbar bg-base-100 shadow-lg">
    <!-- Primary Navigation Menu -->
    <div class="navbar-start">
        <!-- Logo -->
        <div class="flex-shrink-0 flex items-center">
            <a href="{{ route('dashboard') }}" class="btn btn-ghost text-xl">
                <x-application-mark class="block h-9 w-auto" />
                {{ config('app.name', 'Laravel') }}
            </a>
        </div>

        <!-- Navigation Links -->
        <div class="hidden lg:flex">
            <ul class="menu menu-horizontal px-1">
                <li>
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </li>
                <li>
                    <details>
                        <summary>📚 Biblioteca</summary>
                        <ul class="p-2 bg-base-100 rounded-t-none">
                            <li><a href="{{ route('livros.index') }}">📖 Livros</a></li>
                            <li><a href="{{ route('autores.index') }}">✍️ Autores</a></li>
                            <li><a href="{{ route('editoras.index') }}">🏢 Editoras</a></li>
                        </ul>
                    </details>
                </li>
                {{--<li>
                    <details>
                        <summary>👥 Usuários</summary>
                        <ul class="p-2 bg-base-100 rounded-t-none">
                            <li><a href="{{ route('users.index') }}">👥 Listar Usuários</a></li>
                            <li><a href="{{ route('users.create') }}">➕ Novo Usuário</a></li>
                        </ul>
                    </details>
                </li>--}}
                {{-- Só mostra o menu "Usuários" se o utilizador for admin --}}
                    @if (Auth::user()->role === 'admin')
                        <li>
                            <details>
                                <summary>👥 Usuários</summary>
                                <ul class="p-2 bg-base-100 rounded-t-none">
                                    <li><a href="{{ route('users.index') }}">👥 Listar Usuários</a></li>
                                    <li><a href="{{ route('users.create') }}">➕ Novo Usuário</a></li>
                                </ul>
                            </details>
                        </li>
                    @endif
                <li>
                    <details>
                        <summary>📋 Requisições</summary>
                        <ul class="p-2 bg-base-100 rounded-t-none">
                            <li><a href="{{ route('requisicoes.index') }}">📋 Minhas Requisições</a></li>
                            <li><a href="{{ route('requisicoes.create') }}">➕ Nova Requisição</a></li>
                        </ul>
                    </details>
                </li>
                
            </ul>
        </div>
    </div>

    <div class="navbar-end">
        <!-- Settings Dropdown -->
        @auth
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                    <div class="w-10 rounded-full">
                        <img alt="Avatar" src="{{ Auth::user()->profile_photo_url }}" />
                    </div>
                </div>
                <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                    <li class="menu-title">
                        <span>{{ Auth::user()->name }}</span>
                    </li>
                    <li><a href="{{ route('profile.show') }}">⚙️ Perfil</a></li>
                    
                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                        <li><a href="{{ route('api-tokens.index') }}">🔑 API Tokens</a></li>
                    @endif

                    <div class="divider my-1"></div>
                    
                    <!-- Authentication -->
                    <li>
                        <form method="POST" action="{{ route('logout') }}" x-data>
                            @csrf
                            <a href="{{ route('logout') }}"
                                @click.prevent="$root.submit();">
                                🚪 Sair
                            </a>
                        </form>
                    </li>
                </ul>
            </div>
        @else
            <div class="space-x-2">
                <a href="{{ route('login') }}" class="btn btn-ghost">Entrar</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-primary">Registrar</a>
                @endif
            </div>
        @endauth
    </div>

    <!-- Hamburger Menu for Mobile -->
    <div class="navbar-start lg:hidden">
        <div class="dropdown">
            <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </div>
            <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                <li><a href="{{ route('dashboard') }}">🏠 Dashboard</a></li>
                <div class="divider my-1"></div>
                <li><a href="{{ route('livros.index') }}">📖 Livros</a></li>
                <li><a href="{{ route('autores.index') }}">✍️ Autores</a></li>
                <li><a href="{{ route('editoras.index') }}">🏢 Editoras</a></li>
                <li><a href="{{ route('requisicoes.index') }}">📋 Requisições</a></li>
                {{--<li><a href="{{ route('users.index') }}">👥 Usuários</a></li>--}}
                {{-- Também esconde o link no menu mobile --}}
                @if (Auth::user()->role === 'admin')
                    <li><a href="{{ route('users.index') }}">👥 Usuários</a></li>
                @endif

            </ul>
        </div>
    </div>
</nav>