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
                    <li><a>👥 Usuários</a>
                        <ul class="p-2">
                            <li><a href="{{ route('users.index') }}">👥 Listar</a></li>
                            <li><a href="{{ route('users.create') }}">➕ Novo</a></li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
        <!--<a href="{{ route('dashboard') }}" class="btn btn-ghost text-xl normal-case">
            {{ config('app.name', 'Biblioteca') }}
        </a>-->
    </div>

    <!-- Secção Central: Menu Principal para Desktop -->
    <div class="navbar-center hidden lg:flex">
        <ul class="menu menu-horizontal px-1">
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

            @if (auth()->user()->role === 'admin')
                <li>
                    <details>
                        <summary class="text-base">👥 Usuários</summary>
                        <ul class="p-2 bg-base-100 rounded-t-none z-[1]">
                            <li><a href="{{ route('users.index') }}">👥 Listar Usuários</a></li>
                            <li><a href="{{ route('users.create') }}">➕ Novo Usuário</a></li>
                        </ul>
                    </details>
                </li>
            @endif
        </ul>
    </div>

    <!-- Secção Direita: Dropdown do Perfil -->
    <div class="navbar-end">
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
