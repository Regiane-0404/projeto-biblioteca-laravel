<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-base-content">
            👥 Gestão de Usuários
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Mensagens de Feedback --}}
            @if (session('success'))
                <div role="alert" class="alert alert-success mb-6"><span>{{ session('success') }}</span></div>
            @endif
            @if (session('error'))
                <div role="alert" class="alert alert-error mb-6"><span>{{ session('error') }}</span></div>
            @endif

            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="card-title">Lista de Usuários</h3>
                        {{-- O BOTÃO "+ NOVO UTILIZADOR" FOI REMOVIDO DESTA ÁREA --}}
                    </div>

                    <!-- Formulário de Pesquisa e Filtros -->
                    <form method="GET" action="{{ route('users.index') }}" class="mb-4">
                        <div class="flex gap-4 items-end">
                            <div class="form-control flex-grow">
                                <label class="label"><span class="label-text">Pesquisar por Nome ou
                                        Email</span></label>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Digite para pesquisar..." class="input input-bordered w-full">
                            </div>
                            <button type="submit" class="btn btn-primary">🔍 Pesquisar</button>
                            <a href="{{ route('users.index') }}" class="btn btn-ghost" title="Limpar Pesquisa">🔄</a>
                        </div>
                    </form>

                    <!-- Botões de Filtro de Status -->
                    <div class="flex justify-start gap-2 mb-6 border-t pt-4 mt-4 border-base-300">
                        <a href="{{ route('users.index', array_merge(request()->query(), ['status' => null, 'filtro' => null])) }}"
                            class="btn btn-sm {{ !request('status') && !request('filtro') ? 'btn-active' : 'btn-ghost' }}">Todos</a>
                        <a href="{{ route('users.index', array_merge(request()->query(), ['status' => 'ativo', 'filtro' => null])) }}"
                            class="btn btn-sm {{ request('status') == 'ativo' ? 'btn-active btn-success' : 'btn-ghost' }}">Ativos</a>
                        <a href="{{ route('users.index', array_merge(request()->query(), ['status' => 'inativo', 'filtro' => null])) }}"
                            class="btn btn-sm {{ request('status') == 'inativo' ? 'btn-active btn-warning' : 'btn-ghost' }}">Inativos</a>

                        <a href="{{ route('users.index', array_merge(request()->query(), ['filtro' => 'sem_atividade', 'status' => null])) }}"
                            class="btn btn-sm {{ request('filtro') == 'sem_atividade' ? 'btn-active btn-accent' : 'btn-ghost' }}">
                            Sem Atividade
                        </a>
                    </div>

                    <!-- Tabela de Usuários -->
                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <thead>
                                <tr>
                                    <th>
                                        <a
                                            href="{{ route('users.index', array_merge(request()->query(), ['order_by' => 'name', 'order_direction' => request('order_direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Nome</a>
                                    </th>
                                    <th>Email</th>
                                    <th>Perfil</th>
                                    <th>Requisições</th>
                                    <th>Pontos 🏆</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr class="hover">
                                        <td class="font-semibold">{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if ($user->role === 'admin')
                                                <span class="badge badge-error badge-outline">Admin</span>
                                            @else
                                                <span class="badge badge-info badge-outline">Cidadão</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $user->requisicoes_count }}</td>
                                        <td class="font-semibold text-center">{{ $user->pontos }}</td>
                                        <td>
                                            @if ($user->ativo)
                                                <span class="badge badge-success">Ativo</span>
                                            @else
                                                <span class="badge badge-warning">Inativo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="flex items-center gap-2">

                                                @if (request('filtro') === 'sem_atividade')
                                                    @if ($user->id !== auth()->id())
                                                        <form method="POST"
                                                            action="{{ route('users.destroy', $user) }}"
                                                            onsubmit="return confirm('Tem a certeza que deseja EXCLUIR este usuário permanentemente? Esta ação não pode ser desfeita.');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-error"
                                                                title="Excluir Usuário">🗑️ Excluir</button>
                                                        </form>
                                                    @endif
                                                @else
                                                    <a href="{{ route('users.show', $user) }}"
                                                        class="btn btn-sm btn-ghost" title="Ver Detalhes">👁️</a>
                                                    <a href="{{ route('users.edit', $user) }}"
                                                        class="btn btn-sm btn-ghost" title="Editar">✏️</a>

                                                    @if ($user->id !== auth()->id())
                                                        <form method="POST"
                                                            action="{{ route('users.toggle-status', $user) }}"
                                                            onsubmit="return confirm('Tem a certeza que deseja {{ $user->ativo ? 'inativar' : 'ativar' }} este usuário?');">
                                                            @csrf @method('PATCH')
                                                            @if ($user->ativo)
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-ghost text-warning"
                                                                    title="Inativar">⚠️</button>
                                                            @else
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-ghost text-success"
                                                                    title="Ativar">✅</button>
                                                            @endif
                                                        </form>
                                                    @endif
                                                @endif

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-10">
                                            <p>Nenhum usuário encontrado.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
