<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-base-content">
                📖 {{ $livro->nome }}
            </h2>
            <a href="{{ route('livros.index') }}" class="btn btn-ghost">
                ← Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Coluna da Capa -->
                <div class="lg:col-span-1">
                    <div class="card bg-base-100 shadow-xl">
                        <figure class="px-6 pt-6">
                            @if($livro->imagem_capa)
                                <img src="{{ asset('storage/' . $livro->imagem_capa) }}" 
                                     alt="Capa de {{ $livro->nome }}" 
                                     class="rounded-xl w-full max-w-sm h-auto shadow-lg" />
                            @else
                                <div class="w-full max-w-sm h-96 bg-gradient-to-br from-base-300 to-base-200 rounded-xl flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="text-6xl opacity-30 mb-4">📚</div>
                                        <p class="text-base-content/60">Sem capa disponível</p>
                                    </div>
                                </div>
                            @endif
                        </figure>
                        
                        <!-- Preço destacado -->
                        <div class="card-body text-center">
                            <div class="text-3xl font-bold text-success">
                                €{{ number_format($livro->preco, 2, ',', '.') }}
                            </div>
                            <p class="text-sm text-base-content/60">Preço de venda</p>
                        </div>
                    </div>
                </div>

                <!-- Coluna das Informações -->
                <div class="lg:col-span-2">
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            
                            <!-- Título e ISBN -->
                            <div class="mb-6">
                                <h1 class="text-3xl font-bold text-base-content mb-3">
                                    {{ $livro->nome }}
                                </h1>
                                <p class="text-base-content/70 text-lg">
                                    <strong>ISBN:</strong> {{ $livro->isbn }}
                                </p>
                            </div>

                            <!-- Informações principais -->
                            <div class="space-y-6">
                                
                                <!-- Editora -->
                                <div>
                                    <h3 class="font-semibold text-base-content text-lg mb-2">
                                        🏢 Editora
                                    </h3>
                                    @if($livro->editora)
                                        <p class="text-base-content/80 text-lg">{{ $livro->editora->nome }}</p>
                                    @else
                                        <p class="text-base-content/50 italic">Não informado</p>
                                    @endif
                                </div>

                                <!-- Autores -->
                                <div>
                                    <h3 class="font-semibold text-base-content text-lg mb-2">
                                        ✍️ {{ $livro->autores->count() === 1 ? 'Autor' : 'Autores' }}
                                    </h3>
                                    @if($livro->autores->count() > 0)
                                        <div class="text-base-content/80 text-lg">
                                            @foreach($livro->autores as $index => $autor)
                                                {{ $autor->nome }}@if(!$loop->last), @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-base-content/50 italic">Nenhum autor associado</p>
                                    @endif
                                </div>

                                <!-- Bibliografia/Descrição -->
                                @if($livro->bibliografia)
                                    <div>
                                        <h3 class="font-semibold text-base-content text-lg mb-3">
                                            📄 Sobre o livro
                                        </h3>
                                        <div class="bg-base-200/50 rounded-lg p-4 border-l-4 border-primary">
                                            <p class="text-base-content leading-relaxed">
                                                {{ $livro->bibliografia }}
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Data de cadastro -->
                                <div>
                                    <h3 class="font-semibold text-base-content text-lg mb-2">
                                        📅 Cadastrado em
                                    </h3>
                                    <p class="text-base-content/80">
                                        {{ $livro->created_at->format('d/m/Y') }} 
                                        <span class="text-base-content/60">({{ $livro->created_at->diffForHumans() }})</span>
                                    </p>
                                </div>

                            </div>

                            <!-- Botões de ação -->
                            <div class="flex flex-wrap gap-3 mt-8 pt-6 border-t border-base-300">
                                <a href="{{ route('livros.index') }}" class="btn btn-outline">
                                    📋 Todos os Livros
                                </a>
                                <button class="btn btn-outline" onclick="window.print()">
                                    🖨️ Imprimir
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>