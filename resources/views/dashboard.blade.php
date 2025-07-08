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
                                <h2 class="card-title text-3xl">{{ \App\Models\Livro::where('ativo', true)->count() }}</h2>
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

                <!-- Últimas Atividades -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title">📈 Últimas Atividades</h2>
                        <div class="space-y-3 mt-4">
                            @php
                                // Buscar 1 livro de hoje
                                $livroHoje = \App\Models\Livro::with(['autores', 'editora'])
                                                ->where('ativo', true)
                                                ->whereDate('created_at', today())
                                                ->latest()
                                                ->first();
                                
                                // Buscar 1 livro de ontem
                                $livroOntem = \App\Models\Livro::with(['autores', 'editora'])
                                                ->where('ativo', true)
                                                ->whereDate('created_at', today()->subDay())
                                                ->latest()
                                                ->first();
                                
                                // Criar coleção com os livros encontrados
                                $livrosRecentes = collect([$livroHoje, $livroOntem])->filter();
                            @endphp
                            
                            @forelse($livrosRecentes as $livro)
                                <div class="flex items-center space-x-3 p-3 rounded-lg hover:bg-base-200 border border-base-300">
                                    <div class="avatar placeholder">
                                        @if($livro->imagem_capa)
                                            <div class="bg-neutral text-neutral-content rounded-lg w-14 h-14">
                                                <img src="{{ asset('storage/' . $livro->imagem_capa) }}" alt="Capa" class="w-full h-full object-cover rounded-lg" />
                                            </div>
                                        @else
                                            <div class="bg-neutral text-neutral-content rounded-lg w-14 h-14">
                                                <span class="text-xl">📖</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-bold text-base-content text-lg">{{ $livro->nome }}</div>
                                        <div class="text-sm text-base-content/70">
                                            @if($livro->autores->count() > 0)
                                                por {{ $livro->autores->first()->nome }}
                                                @if($livro->autores->count() > 1)
                                                    e mais {{ $livro->autores->count() - 1 }}
                                                @endif
                                            @endif
                                        </div>
                                        <div class="text-xs text-base-content/50 mt-1">
                                            {{ $livro->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="badge badge-primary">€{{ number_format($livro->preco, 2) }}</div>
                                        @if($livro->created_at->isToday())
                                            <div class="badge badge-success">Hoje</div>
                                        @elseif($livro->created_at->isYesterday())
                                            <div class="badge badge-warning">Ontem</div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <div class="text-6xl opacity-20 mb-4">📅</div>
                                    <h3 class="font-bold text-lg mb-2">Sem atividades recentes</h3>
                                    <p class="text-base-content/60 mb-4">Nenhum livro foi cadastrado hoje ou ontem</p>
                                    <a href="{{ route('livros.create') }}" class="btn btn-primary btn-sm">
                                        ➕ Cadastrar Novo Livro
                                    </a>
                                </div>
                            @endforelse
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
                        <div class="stat bg-gradient-to-br from-success/10 to-success/5 rounded-lg p-4 border border-success/20">
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
                        <div class="stat bg-gradient-to-br from-warning/10 to-warning/5 rounded-lg p-4 border border-warning/20">
                            <div class="stat-title text-base-content/70">👑 Livro Mais Valioso</div>
                            @php
                                $livroMaisCaro = \App\Models\Livro::where('ativo', true)
                                ->get()
                                ->sortByDesc(function($livro) {
                                    return (float) $livro->preco; // Laravel desencripta automaticamente
                                })
                                ->first();
                            @endphp
                            @if($livroMaisCaro)
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

                    <!-- Resumo Adicional 
                    <div class="mt-6 pt-4 border-t border-base-300">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                            <div>
                                <div class="text-lg font-bold text-primary">{{ \App\Models\Livro::where('ativo', true)->count() }}</div>
                                <div class="text-xs text-base-content/60">Títulos Ativos</div>
                            </div>
                            <div>
                                <div class="text-lg font-bold text-error">{{ \App\Models\Livro::where('ativo', false)->count() }}</div>
                                <div class="text-xs text-base-content/60">Descatalogados</div>
                            </div>
                            <div>
                                @php
                                    $livroMaisBarato = $livrosAtivos->sortBy('preco')->first();
                                @endphp
                                <div class="text-lg font-bold text-secondary">
                                    €{{ $livroMaisBarato ? number_format($livroMaisBarato->preco, 2, ',', '.') : '0,00' }}
                                </div>
                                <div class="text-xs text-base-content/60">Menor Preço</div>
                            </div>
                            <div>
                                @php
                                    $totalEditoras = \App\Models\Editora::count();
                                @endphp
                                <div class="text-lg font-bold text-accent">{{ $totalEditoras }}</div>
                                <div class="text-xs text-base-content/60">Editoras Parceiras</div>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</x-app-layout>