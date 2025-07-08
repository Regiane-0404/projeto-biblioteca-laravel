<x-app-layout> 
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-base-content">
                ✍️ Autores
            </h2>
            <a href="{{ route('autores.create') }}" class="btn btn-primary">
                ➕ Novo Autor
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Mensagens -->
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
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Card Principal -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    
                    <!-- Pesquisa -->
                    <div class="flex flex-col lg:flex-row gap-4 mb-6">
                        
                        <!-- Barra de Pesquisa -->
                        <form method="GET" action="{{ route('autores.index') }}" class="flex-1">
                            <div class="join w-full">
                                <input 
                                    type="text" 
                                    name="search" 
                                    value="{{ request('search') }}"
                                    placeholder="🔍 Pesquisar por nome do autor..." 
                                    class="input input-bordered join-item flex-1"
                                />
                                <button type="submit" class="btn btn-primary join-item">
                                    Pesquisar
                                </button>
                            </div>
                            
                            <!-- Manter ordenação -->
                            <input type="hidden" name="order_by" value="{{ request('order_by') }}">
                            <input type="hidden" name="order_direction" value="{{ request('order_direction') }}">
                        </form>

                        <!-- Botão Limpar -->
                        @if(request('search'))
                            <a href="{{ route('autores.index') }}" class="btn btn-ghost">
                                🗑️ Limpar
                            </a>
                        @endif
                    </div>

                    <!-- Estatísticas -->
                    <div class="stats stats-horizontal shadow mb-6">
                        <div class="stat">
                            <div class="stat-title">Total de Autores</div>
                            <div class="stat-value text-primary">{{ $autores->total() }}</div>
                        </div>
                        <div class="stat">
                            <div class="stat-title">Mostrando</div>
                            <div class="stat-value text-sm">{{ $autores->firstItem() ?? 0 }} - {{ $autores->lastItem() ?? 0 }}</div>
                        </div>
                    </div>

                    <!-- Lista de Autores -->
                    @if($autores->count() > 0)
                        
                        <!-- Cabeçalho da Tabela -->
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Lista de Autores</h3>
                            
                            <!-- Ordenação -->
                            <div class="dropdown dropdown-end">
                                <div tabindex="0" role="button" class="btn btn-ghost btn-sm">
                                    📊 Ordenar
                                </div>
                                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                    <li>
                                        <a href="{{ route('autores.index', array_merge(request()->query(), ['order_by' => 'nome', 'order_direction' => 'asc'])) }}">
                                            Nome A-Z
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('autores.index', array_merge(request()->query(), ['order_by' => 'nome', 'order_direction' => 'desc'])) }}">
                                            Nome Z-A
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('autores.index', array_merge(request()->query(), ['order_by' => 'created_at', 'order_direction' => 'desc'])) }}">
                                            Mais Recentes
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('autores.index', array_merge(request()->query(), ['order_by' => 'created_at', 'order_direction' => 'asc'])) }}">
                                            Mais Antigos
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Grid de Autores -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach($autores as $autor)
                                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-all duration-200 border border-base-300">
                                    
                                    <!-- Foto do Autor -->
                                    <figure class="px-6 pt-6">
                                        @if($autor->foto)
                                            <img src="{{ asset('storage/' . $autor->foto) }}" 
                                                 alt="Foto de {{ $autor->nome }}" 
                                                 class="rounded-full w-24 h-24 object-cover shadow-lg" />
                                        @else
                                            <div class="w-24 h-24 bg-gradient-to-br from-secondary/20 to-accent/20 rounded-full flex items-center justify-center shadow-lg">
                                                <span class="text-4xl">👤</span>
                                            </div>
                                        @endif
                                    </figure>
                                    
                                    <!-- Informações -->
                                    <div class="card-body text-center p-4">
                                        <h3 class="card-title text-lg justify-center">{{ $autor->nome }}</h3>
                                        
                                        <!-- Estatísticas -->
                                        <div class="text-sm text-base-content/60 mb-3">
                                            <p>{{ $autor->livros->count() }} {{ $autor->livros->count() === 1 ? 'livro' : 'livros' }}</p>
                                            <p class="text-xs">Desde {{ $autor->created_at->format('M Y') }}</p>
                                        </div>
                                        
                                        <!-- Ações -->
                                        <div class="card-actions justify-center gap-1">
                                            <a href="{{ route('autores.edit', $autor) }}" 
                                               class="btn btn-ghost btn-sm hover:bg-warning hover:text-white" 
                                               title="Editar">
                                                ✏️
                                            </a>
                                            <form method="POST" action="{{ route('autores.destroy', $autor) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-ghost btn-sm hover:bg-error hover:text-white" 
                                                        title="Excluir"
                                                        onclick="return confirm('Tem certeza que deseja excluir {{ $autor->nome }}?')">
                                                    🗑️
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Paginação -->
                        <div class="flex justify-center mt-8">
                            {{ $autores->appends(request()->all())->links() }}
                        </div>
                        
                    @else
                        <!-- Estado Vazio -->
                        <div class="text-center py-12">
                            <div class="text-6xl opacity-20 mb-4">✍️</div>
                            <h3 class="text-xl font-bold mb-2">Nenhum autor encontrado</h3>
                            @if(request('search'))
                                <p class="text-base-content/60 mb-4">Tente ajustar a pesquisa</p>
                                <a href="{{ route('autores.index') }}" class="btn btn-ghost">Limpar Pesquisa</a>
                            @else
                                <p class="text-base-content/60 mb-4">Comece adicionando seu primeiro autor</p>
                                <a href="{{ route('autores.create') }}" class="btn btn-primary">➕ Adicionar Primeiro Autor</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
