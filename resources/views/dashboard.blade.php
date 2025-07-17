<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-base-content">
            📊 {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Cards de Estatísticas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Card Livros -->
                <div class="card bg-primary text-primary-content shadow-xl">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="card-title text-3xl">{{ \App\Models\Livro::where('ativo', true)->count() }}
                                </h2>
                                <p class="opacity-90">📖 Livros Ativos</p>
                            </div>
                            <div class="text-6xl opacity-20">📚</div>
                        </div>
                        <div class="card-actions justify-end">
                            <a href="{{ route('livros.index') }}" class="btn btn-secondary btn-sm">Ver Todos</a>
                        </div>
                    </div>
                </div>

                <!-- Card Autores -->
                <div class="card bg-secondary text-secondary-content shadow-xl">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="card-title text-3xl">{{ \App\Models\Autor::count() }}</h2>
                                <p class="opacity-90">✍️ Autores Registrados</p>
                            </div>
                            <div class="text-6xl opacity-20">👥</div>
                        </div>
                        <div class="card-actions justify-end">
                            <a href="{{ route('autores.index') }}" class="btn btn-primary btn-sm">Ver Todos</a>
                        </div>
                    </div>
                </div>

                <!-- Card Editoras -->
                <div class="card bg-accent text-accent-content shadow-xl">
                    <div class="card-body">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="card-title text-3xl">{{ \App\Models\Editora::count() }}</h2>
                                <p class="opacity-90">🏢 Editoras Parceiras</p>
                            </div>
                            <div class="text-6xl opacity-20">🏭</div>
                        </div>
                        <div class="card-actions justify-end">
                            <a href="{{ route('editoras.index') }}" class="btn btn-secondary btn-sm">Ver Todas</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cards de Ações Rápidas -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Ações Rápidas -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title">⚡ Ações Rápidas</h2>
                        <div class="grid grid-cols-1 gap-3 mt-4">
                            <a href="{{ route('livros.create') }}" class="btn btn-outline btn-primary">
                                ➕ Adicionar Novo Livro
                            </a>
                            <a href="{{ route('autores.create') }}" class="btn btn-outline btn-secondary">
                                ➕ Cadastrar Novo Autor
                            </a>
                            <a href="{{ route('editoras.create') }}" class="btn btn-outline btn-accent">
                                ➕ Registrar Nova Editora
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card Últimas Atividades -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title">Últimos Livros Adicionados</h3>
                        <div class="overflow-x-auto">
                            <table class="table">
                                <tbody>
                                    @forelse($livros_recentes ?? [] as $livro)
                                        <tr>
                                            <td>
                                                <div class="font-semibold">{{ $livro->nome }}</div>
                                                <div class="text-xs text-gray-500">Adicionado em
                                                    {{ $livro->created_at->format('d/m/Y') }}</div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td>Nenhum livro foi adicionado recentemente.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatísticas Financeiras -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">📈 Estatísticas do Acervo</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">

                        <!-- Valor Total -->
                        <div
                            class="stat bg-gradient-to-br from-success/10 to-success/5 rounded-lg p-4 border border-success/20">
                            <div class="stat-title text-base-content/70">💰 Valor Total do Acervo</div>
                            <div class="stat-value text-success text-2xl">
                                @php
                                    $livrosAtivos = \App\Models\Livro::where('ativo', true)->get();
                                    $valorTotal = $livrosAtivos->sum('preco');
                                @endphp
                                €{{ number_format($valorTotal, 2, ',', '.') }}
                            </div>
                            <div class="stat-desc text-base-content/60">
                                Patrimônio em livros ativos
                            </div>
                        </div>

                        <!-- Preço Médio -->
                        <div class="stat bg-gradient-to-br from-info/10 to-info/5 rounded-lg p-4 border border-info/20">
                            <div class="stat-title text-base-content/70">📊 Preço Médio por Livro</div>
                            <div class="stat-value text-info text-2xl">
                                @php
                                    $precoMedio = $livrosAtivos->count() > 0 ? $livrosAtivos->avg('preco') : 0;
                                @endphp
                                €{{ number_format($precoMedio, 2, ',', '.') }}
                            </div>
                            <div class="stat-desc text-base-content/60">
                                Valor médio por título
                            </div>
                        </div>

                        <!-- Livro Mais Caro -->
                        <div
                            class="stat bg-gradient-to-br from-warning/10 to-warning/5 rounded-lg p-4 border border-warning/20">
                            <div class="stat-title text-base-content/70">👑 Livro Mais Valioso</div>
                            @php
                                $livroMaisCaro = \App\Models\Livro::where('ativo', true)
                                    ->get()
                                    ->sortByDesc(function ($livro) {
                                        return (float) $livro->preco;
                                    })
                                    ->first();
                            @endphp
                            @if ($livroMaisCaro)
                                <div class="stat-value text-warning text-2xl">
                                    €{{ number_format($livroMaisCaro->preco, 2, ',', '.') }}
                                </div>
                                <div class="stat-desc text-base-content/60">
                                    {{ Str::limit($livroMaisCaro->nome, 25) }}
                                </div>
                            @else
                                <div class="stat-value text-warning text-2xl">-</div>
                                <div class="stat-desc text-base-content/60">Nenhum livro cadastrado</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
