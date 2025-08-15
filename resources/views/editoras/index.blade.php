<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-base-content">
                🏢 Editoras
            </h2>

            {{-- Botão visível apenas para admins --}}
            @if (Auth::user()->role === 'admin')
                <a href="{{ route('editoras.create') }}" class="btn btn-primary">
                    ➕ Nova Editora
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Card Principal -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">

                    <!-- Pesquisa -->
                    <div class="flex flex-col lg:flex-row gap-4 mb-6">

                        <!-- Barra de Pesquisa -->
                        <form method="GET" action="{{ route('editoras.index') }}" class="flex-1">
                            <div class="join w-full">
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="🔍 Pesquisar por nome da editora..."
                                    class="input input-bordered join-item flex-1" />
                                <button type="submit" class="btn btn-primary join-item">
                                    Pesquisar
                                </button>
                            </div>

                            <!-- Manter ordenação -->
                            <input type="hidden" name="order_by" value="{{ request('order_by') }}">
                            <input type="hidden" name="order_direction" value="{{ request('order_direction') }}">
                        </form>

                        <!-- Botão Limpar -->
                        @if (request('search'))
                            <a href="{{ route('editoras.index') }}" class="btn btn-ghost">
                                🗑️ Limpar
                            </a>
                        @endif
                    </div>

                    <!-- Estatísticas -->
                    <div class="stats stats-horizontal shadow mb-6">
                        <div class="stat">
                            <div class="stat-title">Total de Editoras</div>
                            <div class="stat-value text-primary">{{ $editoras->total() }}</div>
                        </div>
                        <div class="stat">
                            <div class="stat-title">Mostrando</div>
                            <div class="stat-value text-sm">{{ $editoras->firstItem() ?? 0 }} -
                                {{ $editoras->lastItem() ?? 0 }}</div>
                        </div>
                    </div>

                    <!-- Lista de Editoras -->
                    @if ($editoras->count() > 0)

                        <!-- Cabeçalho -->
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Lista de Editoras</h3>

                            <!-- Ordenação -->
                            <div class="dropdown dropdown-end">
                                <div tabindex="0" role="button" class="btn btn-ghost btn-sm">
                                    📊 Ordenar
                                </div>
                                <ul tabindex="0"
                                    class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                    <li>
                                        <a
                                            href="{{ route('editoras.index', array_merge(request()->query(), ['order_by' => 'nome', 'order_direction' => 'asc'])) }}">
                                            Nome A-Z
                                        </a>
                                    </li>
                                    <li>
                                        <a
                                            href="{{ route('editoras.index', array_merge(request()->query(), ['order_by' => 'nome', 'order_direction' => 'desc'])) }}">
                                            Nome Z-A
                                        </a>
                                    </li>
                                    <li>
                                        <a
                                            href="{{ route('editoras.index', array_merge(request()->query(), ['order_by' => 'created_at', 'order_direction' => 'desc'])) }}">
                                            Mais Recentes
                                        </a>
                                    </li>
                                    <li>
                                        <a
                                            href="{{ route('editoras.index', array_merge(request()->query(), ['order_by' => 'created_at', 'order_direction' => 'asc'])) }}">
                                            Mais Antigas
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Grid de Editoras -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach ($editoras as $editora)
                                <div
                                    class="card bg-base-100 shadow-lg hover:shadow-xl transition-all duration-200 border border-base-300">

                                    <!-- Logotipo da Editora -->
                                    <figure class="px-6 pt-6">
                                        @if ($editora->logotipo)
                                            <img src="{{ asset('storage/' . $editora->logotipo) }}"
                                                alt="Logo de {{ $editora->nome }}"
                                                class="rounded-lg w-24 h-24 object-contain shadow-lg bg-white p-2" />
                                        @else
                                            <div
                                                class="w-24 h-24 bg-gradient-to-br from-accent/20 to-primary/20 rounded-lg flex items-center justify-center shadow-lg">
                                                <span class="text-4xl">🏢</span>
                                            </div>
                                        @endif
                                    </figure>

                                    <!-- Informações -->
                                    <div class="card-body text-center p-4">
                                        <h3 class="card-title text-lg justify-center">{{ $editora->nome }}</h3>

                                        <!-- Estatísticas -->
                                        <div class="text-sm text-base-content/60 mb-3">
                                            <p>{{ $editora->livros->count() }}
                                                {{ $editora->livros->count() === 1 ? 'livro' : 'livros' }}</p>
                                            <p class="text-xs">Desde {{ $editora->created_at->format('M Y') }}</p>
                                        </div>

                                        <!-- Ações -->
                                        <div class="card-actions justify-center gap-1">

                                            {{-- Botões visíveis apenas para admins --}}
                                            @if (Auth::user()->role === 'admin')
                                                <a href="{{ route('editoras.edit', $editora) }}"
                                                    class="btn btn-ghost btn-sm hover:bg-warning hover:text-white"
                                                    title="Editar">
                                                    ✏️
                                                </a>
                                                <form method="POST" action="{{ route('editoras.destroy', $editora) }}"
                                                    class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-ghost btn-sm hover:bg-error hover:text-white"
                                                        title="Excluir"
                                                        onclick="return confirm('Tem certeza que deseja excluir {{ $editora->nome }}?')">
                                                        🗑️
                                                    </button>
                                                </form>
                                            @endif

                                        </div> <!-- Fim card-actions -->
                                    </div> <!-- Fim card-body -->
                                </div> <!-- Fim card -->
                            @endforeach
                        </div>

                        <!-- Paginação -->
                        <div class="flex justify-center mt-8">
                            {{ $editoras->appends(request()->all())->links() }}
                        </div>
                    @else
                        <!-- Estado Vazio -->
                        <div class="text-center py-12">
                            <div class="text-6xl opacity-20 mb-4">🏢</div>
                            <h3 class="text-xl font-bold mb-2">Nenhuma editora encontrada</h3>
                            @if (request('search'))
                                <p class="text-base-content/60 mb-4">Tente ajustar a pesquisa</p>
                                <a href="{{ route('editoras.index') }}" class="btn btn-ghost">Limpar Pesquisa</a>
                            @else
                                <p class="text-base-content/60 mb-4">Comece adicionando sua primeira editora</p>
                                <a href="{{ route('editoras.create') }}" class="btn btn-primary">➕ Adicionar Primeira
                                    Editora</a>
                            @endif
                        </div>
                    @endif

                </div> <!-- Fim card-body principal -->
            </div> <!-- Fim card principal -->

        </div>
    </div>
</x-app-layout>
