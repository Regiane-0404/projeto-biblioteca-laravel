<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="emerald">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>

<body class="font-sans antialiased bg-base-200">
    <x-banner />

    <div class="min-h-screen">
        @livewire('navigation-menu')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-base-100 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        {{ $header }}

                        <!-- Seletor de Tema -->
                        <div class="dropdown dropdown-end">
                            <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <ul tabindex="0"
                                class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                <li><a onclick="changeTheme('light')">🌞 Light</a></li>
                                <li><a onclick="changeTheme('dark')">🌙 Dark</a></li>
                                <li><a onclick="changeTheme('emerald')">💚 Emerald</a></li>
                                <li><a onclick="changeTheme('corporate')">🏢 Corporate</a></li>
                                <li><a onclick="changeTheme('synthwave')">🌈 Synthwave</a></li>
                                <li><a onclick="changeTheme('cyberpunk')">🤖 Cyberpunk</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="bg-base-200 min-h-screen">

            {{-- ========================================================= --}}
            {{-- == INÍCIO DO BLOCO DE MENSAGENS (COLE AQUI)          == --}}
            {{-- ========================================================= --}}
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-4">
                {{-- Mensagem de Sucesso --}}
                @if (session('success'))
                    <div class="alert alert-success shadow-lg mb-4">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6"
                                fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                {{-- Mensagem de Erro (do back() ou de validação) --}}
                @if (session('error') || !$errors->isEmpty())
                    <div class="alert alert-error shadow-lg mb-4">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6"
                                fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>
                                @if (session('error'))
                                    {{ session('error') }}
                                @else
                                    Houve alguns erros com os seus dados. Por favor, verifique os campos em falta.
                                @endif
                            </span>
                        </div>
                    </div>
                @endif
            </div>
            {{-- ========================================================= --}}
            {{-- == FIM DO BLOCO DE MENSAGENS                           == --}}
            {{-- ========================================================= --}}

            {{ $slot }}
        </main>
    </div>

    @stack('modals')

    @livewireScripts

    <script>
        // Função para trocar tema
        function changeTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
        }

        // Carregar tema salvo
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'emerald';
            document.documentElement.setAttribute('data-theme', savedTheme);
        });
    </script>

    {{-- ========================================================= --}}
    {{-- == MODAL DE CRIAÇÃO DE UTILIZADOR                      == --}}
    {{-- ========================================================= --}}
    @if (auth()->check() && auth()->user()->role === 'admin')
        <dialog id="modal_create_user" class="modal">
            <div class="modal-box w-11/12 max-w-2xl">
                <form method="POST" action="{{ route('users.store') }}">
                    @csrf
                    <h3 class="font-bold text-lg mb-4">➕ Criar Novo Utilizador</h3>

                    <div class="space-y-4">
                        <!-- Nome -->
                        <div class="form-control">
                            <label class="label"><span class="label-text">Nome</span></label>
                            <input type="text" name="name" placeholder="Nome completo do utilizador"
                                class="input input-bordered w-full" value="{{ old('name') }}" required />
                            @error('name')
                                <span class="text-error text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-control">
                            <label class="label"><span class="label-text">Email</span></label>
                            <input type="email" name="email" placeholder="email@exemplo.com"
                                class="input input-bordered w-full" value="{{ old('email') }}" required />
                            @error('email')
                                <span class="text-error text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Role -->
                        <div class="form-control">
                            <label class="label"><span class="label-text">Papel / Função</span></label>
                            <select name="role" class="select select-bordered w-full" required>
                                <option value="cidadao" {{ old('role') === 'cidadao' ? 'selected' : '' }}>Cidadão
                                </option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrador
                                </option>
                            </select>
                            @error('role')
                                <span class="text-error text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="form-control">
                            <label class="label"><span class="label-text">Password</span></label>
                            <input type="password" name="password" class="input input-bordered w-full" required />
                            @error('password')
                                <span class="text-error text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Confirmar Password -->
                        <div class="form-control">
                            <label class="label"><span class="label-text">Confirmar Password</span></label>
                            <input type="password" name="password_confirmation" class="input input-bordered w-full"
                                required />
                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="modal-action mt-6">
                        <a href="#" class="btn btn-ghost"
                            onclick="event.preventDefault(); document.getElementById('modal_create_user').close();">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">Salvar Utilizador</button>
                    </div>
                </form>
            </div>
        </dialog>
    @endif
    {{-- ========================================================= --}}
    {{-- == FIM DO MODAL                                        == --}}
    {{-- ========================================================= --}}
</body>

</html>
