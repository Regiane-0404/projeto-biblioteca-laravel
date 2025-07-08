<x-guest-layout>
    <div class="min-h-screen bg-base-200 flex items-center justify-center">
        <div class="card w-full max-w-md bg-base-100 shadow-2xl">
            <div class="card-body">
                <!-- Logo -->
                <div class="text-center mb-6">
                    <h1 class="text-3xl font-bold text-primary">📚 Sistema Biblioteca</h1>
                    <p class="text-base-content/60 mt-2">Entre com sua conta</p>
                </div>

                <x-validation-errors class="mb-4" />

                @if (session('status'))
                    <div class="alert alert-info mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="form-control mb-4">
                        <label class="label" for="email">
                            <span class="label-text">📧 Email</span>
                        </label>
                        <input id="email" class="input input-bordered w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="seu@email.com" />
                    </div>

                    <!-- Password -->
                    <div class="form-control mb-4">
                        <label class="label" for="password">
                            <span class="label-text">🔒 Senha</span>
                        </label>
                        <input id="password" class="input input-bordered w-full" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                    </div>

                    <!-- Remember Me -->
                    <div class="form-control mb-4">
                        <label class="cursor-pointer label justify-start gap-3">
                            <input type="checkbox" name="remember" class="checkbox checkbox-primary" />
                            <span class="label-text">Lembrar-me</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        @if (Route::has('password.request'))
                            <a class="link link-primary text-sm" href="{{ route('password.request') }}">
                                Esqueceu sua senha?
                            </a>
                        @endif

                        <button class="btn btn-primary" type="submit">
                            Entrar
                        </button>
                    </div>
                </form>

                @if (Route::has('register'))
                    <div class="divider">OU</div>
                    <div class="text-center">
                        <span class="text-base-content/60">Não tem conta? </span>
                        <a href="{{ route('register') }}" class="link link-primary">Registre-se aqui</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-guest-layout>