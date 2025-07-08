<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
    <h2 class="text-2xl font-bold text-base-content">
        📖 Livros
    </h2>
    <div class="flex gap-2">
        <a href="{{ route('livros.create') }}" class="btn btn-primary">
            ➕ Novo Livro
        </a>
        <a href="{{ route('livros.exportar', request()->query()) }}" class="btn btn-success">
            📊 Exportar CSV
        </a>
    </div>
</div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Mensagens de Feedback Completas -->
        @if(session('success'))
            <div class="alert alert-success mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span style="white-space: pre-line;">{{ session('error') }}</span>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

            <!-- Card Principal -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    
                    <!-- Filtros e Pesquisa -->
                    <div class="flex flex-col lg:flex-row gap-4 mb-6">
                        
                        <!-- Pesquisa -->
                        <form method="GET" action="{{ route('livros.index') }}" class="flex-1">
                            <div class="join w-full">
                                <input 
                                    type="text" 
                                    name="search" 
                                    value="{{ request('search') }}"
                                    placeholder="🔍 Pesquisar por nome ou ISBN..." 
                                    class="input input-bordered join-item flex-1"
                                />
                                <button type="submit" class="btn btn-primary join-item">
                                    Pesquisar
                                </button>
                            </div>
                            
                            <!-- Manter outros filtros -->
                            <input type="hidden" name="editora" value="{{ request('editora') }}">
                            <input type="hidden" name="order_by" value="{{ request('order_by') }}">
                            <input type="hidden" name="order_direction" value="{{ request('order_direction') }}">
                        </form>

                        <!-- Filtro por Editora -->
                        <form method="GET" action="{{ route('livros.index') }}" class="min-w-[200px]">
                            <select name="editora" class="select select-bordered w-full" onchange="this.form.submit()">
                                <option value="">🏢 Todas as Editoras</option>
                                @foreach($editoras as $editora)
                                    <option value="{{ $editora->id }}" {{ request('editora') == $editora->id ? 'selected' : '' }}>
                                        {{ $editora->nome }}
                                    </option>
                                @endforeach
                            </select>
                            
                            <!-- Manter outros filtros -->
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="order_by" value="{{ request('order_by') }}">
                            <input type="hidden" name="order_direction" value="{{ request('order_direction') }}">
                        </form>

                        <!-- Botão Limpar Filtros -->
                        @if(request('search') || request('editora'))
                            <a href="{{ route('livros.index') }}" class="btn btn-ghost">
                                🗑️ Limpar
                            </a>
                        @endif
                    </div>

                    <!-- Estatísticas -->
                    <div class="stats stats-horizontal shadow mb-6">
                        <div class="stat">
                            <div class="stat-title">Total de Livros</div>
                            <div class="stat-value text-primary">{{ $livros->total() }}</div>
                        </div>
                        <div class="stat">
                            <div class="stat-title">Mostrando</div>
                            <div class="stat-value text-sm">{{ $livros->firstItem() ?? 0 }} - {{ $livros->lastItem() ?? 0 }}</div>
                        </div>
                    </div>

                    <!-- Tabela -->
                  <!-- Tabela -->
                    @if($livros->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="table w-full">
                                <thead>
                                    <tr class="border-b-2">
                                        <th class="text-left">
                                            <a href="{{ route('livros.index', array_merge(request()->query(), ['order_by' => 'nome', 'order_direction' => request('order_direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                                               class="flex items-center gap-2 hover:text-primary">
                                                📖 Nome
                                                @if(request('order_by') == 'nome')
                                                    <span class="text-xs">{{ request('order_direction') == 'asc' ? '↑' : '↓' }}</span>
                                                @endif
                                            </a>
                                        </th>
                                        <th class="text-left">
                                            <a href="{{ route('livros.index', array_merge(request()->query(), ['order_by' => 'isbn', 'order_direction' => request('order_direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                                               class="flex items-center gap-2 hover:text-primary">
                                                🏷️ ISBN
                                                @if(request('order_by') == 'isbn')
                                                    <span class="text-xs">{{ request('order_direction') == 'asc' ? '↑' : '↓' }}</span>
                                                @endif
                                            </a>
                                        </th>
                                        <th class="text-left">🏢 Editora</th>
                                        <th class="text-left">✍️ Autores</th>
                                        <th class="text-left">
                                            <a href="{{ route('livros.index', array_merge(request()->query(), ['order_by' => 'preco', 'order_direction' => request('order_direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                                               class="flex items-center gap-2 hover:text-primary">
                                                💰 Preço
                                                @if(request('order_by') == 'preco')
                                                    <span class="text-xs">{{ request('order_direction') == 'asc' ? '↑' : '↓' }}</span>
                                                @endif
                                            </a>
                                        </th>
                                        <th class="text-left">🔧 Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($livros as $livro)
                                        <tr class="hover:bg-base-200 border-b">
                                            <!-- Nome do Livro -->
                                            <td class="py-4">
                                                <div class="flex items-center gap-3">
                                                    @if($livro->imagem_capa)
                                                        <div class="avatar">
                                                            <div class="mask mask-squircle w-12 h-12">
                                                                <img src="{{ asset('storage/' . $livro->imagem_capa) }}" alt="Capa" />
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="avatar placeholder">
                                                            <div class="bg-neutral text-neutral-content rounded-lg w-12 h-12">
                                                                <span class="text-xl">📚</span>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="max-w-xs">
                                                        <div class="font-bold text-base-content">{{ $livro->nome }}</div>
                                                        @if($livro->bibliografia)
                                                            <div class="text-sm text-base-content/70 line-clamp-2">
                                                                {{ Str::limit($livro->bibliografia, 60) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            <!-- ISBN -->
                                            <td class="py-4">
                                                <div class="font-mono text-sm text-base-content break-all">
                                                    {{ $livro->isbn }}
                                                </div>
                                            </td>
                                            
                                            <!-- Editora -->
                                            <td class="py-4">
                                                @if($livro->editora)
                                                    <div class="badge badge-ghost badge-sm">
                                                        {{ Str::limit($livro->editora->nome, 15) }}
                                                    </div>
                                                @else
                                                    <span class="text-base-content/50">Sem editora</span>
                                                @endif
                                            </td>
                                            
                                            <!-- Autores -->
                                            <td class="py-4">
                                                @if($livro->autores->count() > 0)
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach($livro->autores as $autor)
                                                            <div class="badge badge-ghost badge-sm">
                                                                {{ Str::limit($autor->nome, 12) }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-base-content/50">Sem autores</span>
                                                @endif
                                            </td>
                                            
                                            <!-- Preço -->
                                            <td class="py-4">
                                                <span class="font-bold text-success">
                                                    €{{ number_format($livro->preco, 2, ',', '.') }}
                                                </span>
                                            </td>
                                            
                                            <!-- Ações -->
                                            <td class="py-4">
                                                <div class="flex gap-1">
                                                    <a href="{{ route('livros.show', $livro) }}" 
                                                       class="btn btn-ghost btn-sm hover:bg-info hover:text-white" 
                                                       title="Visualizar">
                                                        👁️
                                                    </a>
                                                    <a href="{{ route('livros.edit', $livro) }}" 
                                                       class="btn btn-ghost btn-sm hover:bg-warning hover:text-white" 
                                                       title="Editar">
                                                        ✏️
                                                    </a>
                                                    <form method="POST" action="{{ route('livros.destroy', $livro) }}" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-ghost btn-sm hover:bg-error hover:text-white" 
                                                                title="Excluir"
                                                                onclick="return confirm('Tem certeza que deseja excluir este livro?')">
                                                            🗑️
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        <div class="flex justify-center mt-6">
                            {{ $livros->links() }}
                        </div>
                        
                    @else
                        <!-- Estado Vazio -->
                        <div class="text-center py-12">
                            <div class="text-6xl opacity-20 mb-4">📚</div>
                            <h3 class="text-xl font-bold mb-2">Nenhum livro encontrado</h3>
                            @if(request('search') || request('editora'))
                                <p class="text-base-content/60 mb-4">Tente ajustar os filtros ou limpar a pesquisa</p>
                                <a href="{{ route('livros.index') }}" class="btn btn-ghost">Limpar Filtros</a>
                            @else
                                <p class="text-base-content/60 mb-4">Comece adicionando seu primeiro livro</p>
                                <a href="{{ route('livros.create') }}" class="btn btn-primary">➕ Adicionar Primeiro Livro</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>