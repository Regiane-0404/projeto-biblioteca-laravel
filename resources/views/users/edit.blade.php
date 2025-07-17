<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            ✏️ Editar Usuário
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensagens de Erro -->
            @if ($errors->any())
                <div class="alert alert-error mb-6">
                    <span>❌ Corrija os erros abaixo:</span>
                    <ul class="list-disc list-inside mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Formulário -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h3 class="card-title text-2xl mb-6">✏️ Editar: {{ $user->name }}</h3>

                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Primeira Coluna -->
                            <div>
                                <div class="form-control mb-4">
                                    <label class="label"><span class="label-text font-semibold">Nome Completo
                                            *</span></label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                        placeholder="Ex: João Silva"
                                        class="input input-bordered @error('name') input-error @enderror" required>
                                    @error('name')
                                        <label class="label"><span
                                                class="label-text-alt text-error">{{ $message }}</span></label>
                                    @enderror
                                </div>
                                <div class="form-control mb-4">
                                    <label class="label"><span class="label-text font-semibold">Email *</span></label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                        placeholder="Ex: joao@exemplo.com"
                                        class="input input-bordered @error('email') input-error @enderror" required>
                                    @error('email')
                                        <label class="label"><span
                                                class="label-text-alt text-error">{{ $message }}</span></label>
                                    @enderror
                                </div>
                            </div>
                            <!-- Segunda Coluna -->
                            <div>
                                <div class="form-control mb-4">
                                    <label class="label"><span class="label-text font-semibold">Perfil de Usuário
                                            *</span></label>
                                    <select name="role"
                                        class="select select-bordered @error('role') select-error @enderror" required
                                        {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                        <option value="cidadao"
                                            {{ old('role', $user->role) === 'cidadao' ? 'selected' : '' }}>👤 Cidadão
                                        </option>
                                        <option value="admin"
                                            {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>👑
                                            Administrador</option>
                                    </select>
                                    @if ($user->id === auth()->id())
                                        <input type="hidden" name="role" value="{{ $user->role }}">
                                        <label class="label"><span class="label-text-alt text-warning">⚠️ Você não pode
                                                alterar seu próprio perfil</span></label>
                                    @endif
                                    @error('role')
                                        <label class="label"><span
                                                class="label-text-alt text-error">{{ $message }}</span></label>
                                    @enderror
                                </div>
                                <div class="form-control mb-4">
                                    <label class="label"><span class="label-text font-semibold">Status</span></label>
                                    <div class="form-control">
                                        <label class="label cursor-pointer justify-start">
                                            <input type="checkbox" name="ativo" value="1"
                                                class="checkbox checkbox-success mr-3"
                                                {{ old('ativo', $user->ativo) ? 'checked' : '' }}
                                                {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                            <span class="label-text">✅ Usuário ativo</span>
                                        </label>
                                        @if ($user->id === auth()->id())
                                            <input type="hidden" name="ativo" value="1">
                                            <label class="label"><span class="label-text-alt text-warning">⚠️ Você não
                                                    pode inativar sua própria conta</span></label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Seção de Senha (opcional) -->
                        <div class="divider">Alterar Senha</div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-control mb-6">
                                <label class="label"><span class="label-text font-semibold">Nova Senha</span></label>
                                <input type="password" name="password" placeholder="Deixe em branco para manter"
                                    class="input input-bordered @error('password') input-error @enderror">
                                @error('password')
                                    <label class="label"><span
                                            class="label-text-alt text-error">{{ $message }}</span></label>
                                @enderror
                            </div>
                            <div class="form-control mb-6">
                                <label class="label"><span class="label-text font-semibold">Confirmar Nova
                                        Senha</span></label>
                                <input type="password" name="password_confirmation" placeholder="Confirme a nova senha"
                                    class="input input-bordered">
                            </div>
                        </div>

                        <!-- Informações do usuário -->
                        <div class="alert alert-info mb-6">
                            <span>ℹ️ <strong>Informações do usuário:</strong></span>
                            <ul class="list-disc list-inside mt-2">
                                <li><strong>Cadastrado em:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</li>
                                <li><strong>Última atualização:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}
                                </li>
                                <!-- ======================================================== -->
                                <!--          AQUI ESTÃO AS DUAS CORREÇÕES                  -->
                                <!-- ======================================================== -->
                                <li><strong>Total de requisições:</strong> {{ $user->requisicoes->count() }}</li>
                                @if ($user->requisicoesAtivas->count() > 0)
                                    <li><strong>Requisições ativas:</strong> {{ $user->requisicoesAtivas->count() }}
                                    </li>
                                @endif
                            </ul>
                        </div>

                        <!-- Botões -->
                        <div class="card-actions justify-end">
                            <a href="{{ route('users.index') }}" class="btn btn-outline">Cancelar</a>
                            <button type="submit" class="btn btn-primary">✏️ Atualizar Usuário</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
