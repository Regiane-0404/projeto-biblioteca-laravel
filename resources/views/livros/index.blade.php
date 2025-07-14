<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-base-content">
                📖 Livros Cadastrados
            </h2>

            {{-- Só mostra os botões de ação se o utilizador for admin --}}
            @if (auth()->user()->role === 'admin')
                <div class="flex gap-2">
                    <a href="{{ route('livros.create') }}" class="btn btn-primary">
                        ➕ Novo Livro
                    </a>
                    <a href="{{ route('livros.exportar', request()->query()) }}" class="btn btn-success">
                        📊 Exportar CSV
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Mensagens de Feedback -->
            @if(session('success'))
                <div role="alert" class="alert alert-success mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div role="alert" class="alert alert-error mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if(session('warning'))
                 <div role="alert" class="alert alert-warning mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            <!-- Card Principal com Tabela -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                
                    <!-- Formulário de Filtros e Pesquisa -->
                    <form method="GET" action="{{ route('livros.index') }}" class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <!-- Pesquisa -->
                            <div class="form-control md:col-span-2">
                                <label class="label"><span class="label-text">Pesquisar por Título ou ISBN</span></label>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Digite para pesquisar..." class="input input-bordered w-full">
                            </div>
                            <!-- Filtro por Editora -->
                            <div class="form-control">
                                <label class="label"><span class="label-text">Filtrar por Editora</span></label>
                                <select name="editora" class="select select-bordered w-full">
                                    <option value="">Todas</option>
                                    @foreach($editoras as $editora)
                                        <option value="{{ $editora->id }}" {{ request('editora') == $editora->id ? 'selected' : '' }}>
                                            {{ $editora->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Ações do Formulário -->
                            <div class="flex gap-2">
                                <button type="submit" class="btn btn-primary flex-grow">🔍 Filtrar</button>
                                <a href="{{ route('livros.index') }}" class="btn btn-outline" title="Limpar Filtros">🔄</a>
                            </div>
                        </div>
                    </form> {{-- << FIM DO FORMULÁRIO --}}

                    <!-- Botões de Status (Ativo, Inativo, Todos) -->
                    <div class="flex justify-start gap-2 mb-6 border-t pt-4 mt-4">
                        <a href="{{ route('livros.index', array_merge(request()->query(), ['status' => 'ativo'])) }}" 
                           class="btn btn-sm {{ request('status', 'ativo') == 'ativo' ? 'btn-success' : 'btn-ghost' }}">
                            Ativos
                        </a>
                        <a href="{{ route('livros.index', array_merge(request()->query(), ['status' => 'inativo'])) }}" 
                           class="btn btn-sm {{ request('status') == 'inativo' ? 'btn-warning' : 'btn-ghost' }}">
                            Inativos
                        </a>
                        <a href="{{ route('livros.index', array_merge(request()->query(), ['status' => 'todos'])) }}" 
                           class="btn btn-sm {{ request('status') == 'todos' ? 'btn-ghost' : 'btn-ghost' }}">
                            Todos
                        </a>
                    </div>

                    <!-- Tabela de Livros -->
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>Livro / ISBN</th>
                                    <th>Editora</th>
                                    <th>Autores</th>
                                    <th class="text-right">Preço</th>
                                    <th>Status</th>
                                    <th class="w-1">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($livros as $livro)
                                    <tr class="hover">
                                        <td>
                                            <div class="flex items-center gap-3">
                                                <div class="avatar">
                                                    <div class="mask mask-squircle w-12 h-12">
                                                        @if($livro->imagem_capa && Storage::disk('public')->exists($livro->imagem_capa))
                                                            <img src="{{ asset('storage/' . $livro->imagem_capa) }}" alt="Capa de {{ $livro->nome }}">
                                                        @else
                                                            <div class="w-12 h-12 bg-base-200 flex items-center justify-center"><span class="text-xl opacity-40">📚</span></div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-bold">{{ $livro->nome }}</div>
                                                    <div class="text-sm opacity-50">{{ $livro->isbn }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $livro->editora->nome ?? 'N/A' }}</td>
                                        <td>{{ $livro->autores->pluck('nome')->join(', ') ?: 'N/A' }}</td>
                                        <td class="font-mono text-right">€{{ number_format($livro->preco, 2, ',', '.') }}</td>
                                        <td>
                                            @if($livro->ativo)
                                                <span class="badge badge-success badge-outline">Ativo</span>
                                            @else
                                                <span class="badge badge-warning badge-outline">Inativo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="flex gap-2">
                                                <button class="btn btn-sm btn-outline btn-accent" title="Ver Detalhes (Em Desenvolvimento)" disabled>👁️</button>
                                                @if(auth()->user()->role === 'admin')
                                                    <a href="{{ route('livros.edit', $livro->id) }}" class="btn btn-sm btn-outline btn-info" title="Editar">✏️</a>
                                                    
                                                    @if($livro->ativo)
                                                        <form action="{{ route('livros.inativar', $livro->id) }}" method="POST" onsubmit="return confirm('Tem a certeza que deseja inativar este livro?')">
                                                            @csrf @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-outline btn-warning" title="Inativar">⚠️</button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('livros.ativar', $livro->id) }}" method="POST">
                                                            @csrf @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-outline btn-success" title="Ativar">✅</button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-8">
                                            <p class="text-lg text-base-content/50">Nenhum livro encontrado com os filtros atuais.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    <div class="mt-6">
                        {{ $livros->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
