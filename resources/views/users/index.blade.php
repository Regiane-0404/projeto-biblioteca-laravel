<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-base-content">
                👥 Gestão de Usuários
            </h2>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                ➕ Novo Usuário
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Mensagens de Feedback -->
            @if (session('success'))
                <div role="alert" class="alert alert-success mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div role="alert" class="alert alert-error mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Card Principal -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Perfil</th>
                                    <th>Requisições</th>
                                    <th>Status</th>
                                    <th class="w-1">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr class="hover">
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if ($user->role === 'admin')
                                                <span class="badge badge-error badge-outline">👑 Admin</span>
                                            @else
                                                <span class="badge badge-info badge-outline">👤 Cidadão</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge-ghost">{{ $user->requisicoes_count ?? $user->requisicoes->count() }}</span>
                                        </td>
                                        <td>
                                            @if ($user->ativo)
                                                <span class="badge badge-success">Ativo</span>
                                            @else
                                                <span class="badge badge-warning">Inativo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="flex items-center gap-2">
                                                <!-- BOTÃO PARA VER DETALHES -->
                                                <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-ghost"
                                                    title="Ver Detalhes e Histórico">👁️</a>

                                                <a href="{{ route('users.edit', $user) }}"
                                                    class="btn btn-sm btn-outline btn-info" title="Editar">✏️</a>

                                                {{-- Lógica para não poder inativar a si mesmo --}}
                                                @if ($user->id !== auth()->id())
                                                    <form method="POST"
                                                        action="{{ route('users.toggle-status', $user) }}"
                                                        onsubmit="return confirm('Tem a certeza que deseja {{ $user->ativo ? 'inativar' : 'ativar' }} este usuário?');">
                                                        @csrf
                                                        @method('PATCH')
                                                        @if ($user->ativo)
                                                            <button type="submit"
                                                                class="btn btn-sm btn-outline btn-warning"
                                                                title="Inativar">⚠️</button>
                                                        @else
                                                            <button type="submit"
                                                                class="btn btn-sm btn-outline btn-success"
                                                                title="Ativar">✅</button>
                                                        @endif
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-8">
                                            <p class="text-lg text-base-content/50">Nenhum usuário encontrado.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
