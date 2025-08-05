<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            ➕ Novo Usuário
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm-px-6 lg-px-8">
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
                    <h3 class="card-title text-2xl mb-6">👤 Criar Novo Usuário</h3>

                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Primeira Coluna -->
                            <div>
                                <div class="form-control mb-4">
                                    <label class="label">
                                        <span class="label-text font-semibold">Nome Completo *</span>
                                    </label>
                                    <input type="text" name="name" value="{{ old('name') }}"
                                        placeholder="Ex: João Silva"
                                        class="input input-bordered @error('name') input-error @enderror" required>
                                    @error('name')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <div class="form-control mb-4">
                                    <label class="label">
                                        <span class="label-text font-semibold">Email *</span>
                                    </label>
                                    <input type="email" name="email" value="{{ old('email') }}"
                                        placeholder="Ex: joao@exemplo.com"
                                        class="input input-bordered @error('email') input-error @enderror" required>
                                    @error('email')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>
                            </div>

                            <!-- Segunda Coluna -->
                            <div>
                                <div class="form-control mb-4">
                                    <label class="label">
                                        <span class="label-text font-semibold">Perfil de Usuário *</span>
                                    </label>
                                    <select name="role"
                                        class="select select-bordered @error('role') select-error @enderror" required>
                                        <option value="">Selecione o perfil...</option>
                                        <option value="cidadao" {{ old('role') === 'cidadao' ? 'selected' : '' }}>
                                            👤 Cidadão (Pode requisitar livros)
                                        </option>
                                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>
                                            👑 Administrador (Controle total)
                                        </option>
                                    </select>
                                    @error('role')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <div class="form-control mb-4">
                                    <label class="label">
                                        <span class="label-text font-semibold">Status</span>
                                    </label>
                                    <div class="form-control">
                                        <label class="label cursor-pointer justify-start">
                                            <input type="checkbox" name="ativo" value="1"
                                                class="checkbox checkbox-success mr-3"
                                                {{ old('ativo', true) ? 'checked' : '' }}>
                                            <span class="label-text">✅ Usuário ativo (pode fazer login)</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Seção de Senha (largura completa) -->
                        <div class="divider">Configuração de Acesso</div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-control mb-6">
                                <label class="label">
                                    <span class="label-text font-semibold">Senha *</span>
                                </label>
                                <input type="password" name="password" placeholder="Mínimo 6 caracteres"
                                    class="input input-bordered @error('password') input-error @enderror" required>
                                @error('password')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <div class="form-control mb-6">
                                <label class="label">
                                    <span class="label-text font-semibold">Confirmar Senha *</span>
                                </label>
                                <input type="password" name="password_confirmation"
                                    placeholder="Digite a senha novamente" class="input input-bordered" required>
                            </div>
                        </div>

                        <!-- Informações -->
                        <div class="alert alert-info mb-6">
                            <span>ℹ️ <strong>Informações importantes:</strong></span>
                            <ul class="list-disc list-inside mt-2">
                                <li><strong>Administradores:</strong> Podem gerenciar livros, autores, editoras e
                                    usuários</li>
                                <li><strong>Cidadãos:</strong> Podem apenas requisitar livros e ver suas próprias
                                    requisições</li>
                                <li><strong>Usuários inativos:</strong> Não conseguem fazer login no sistema</li>
                            </ul>
                        </div>

                        <!-- Botões -->
                        <div class="card-actions justify-end">
                            <a href="{{ route('users.index') }}" class="btn btn-outline">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                ➕ Criar Usuário
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>```
