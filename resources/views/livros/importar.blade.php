<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-base-content">
            🔎 Importar Livros da Google API
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div role="alert" class="alert alert-success mb-6"><span>{{ session('success') }}</span></div>
            @endif
            @if (session('error'))
                <div role="alert" class="alert alert-error mb-6"><span>{{ session('error') }}</span></div>
            @endif

            <!-- Card do Formulário de Pesquisa (sem alterações) -->
            <div class="card bg-base-100 shadow-xl mb-6">
                <div class="card-body">
                    <form method="GET" action="{{ route('livros.importar.pesquisar') }}">
                        <div class="flex items-end gap-4">
                            <div class="form-control flex-grow">
                                <label class="label"><span class="label-text">Pesquisar por Título, Autor ou
                                        ISBN</span></label>
                                <input type="text" name="termo_pesquisa" value="{{ $termo_pesquisa ?? '' }}"
                                    placeholder="Ex: Duna, Isaac Asimov, 978..." class="input input-bordered w-full"
                                    required minlength="3">
                            </div>
                            <button type="submit" class="btn btn-primary">Pesquisar na Google</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Área de Resultados -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h3 class="card-title">Resultados da Pesquisa para "{{ $termo_pesquisa ?? '...' }}"</h3>

                    <div class="mt-4 space-y-4">
                        @if (isset($resultados))
                            @forelse($resultados as $livro)
                                <div class="card card-side bg-base-200 shadow-sm">
                                    <figure class="p-4">
                                        @if ($livro->capa_url)
                                            <img src="{{ $livro->capa_url }}" alt="Capa de {{ $livro->titulo }}"
                                                class="w-24 rounded" />
                                        @else
                                            <div
                                                class="w-24 h-32 bg-base-300 flex items-center justify-center rounded text-center text-xs p-2">
                                                Sem capa</div>
                                        @endif
                                    </figure>
                                    <div class="card-body">
                                        <h4 class="card-title text-base">{{ $livro->titulo }}</h4>
                                        <p class="text-sm"><strong>Autor(es):</strong> {{ $livro->autores }}</p>
                                        <p class="text-sm"><strong>Editora:</strong> {{ $livro->editora }}</p>
                                        <p class="text-sm font-mono"><strong>ISBN:</strong>
                                            {{ $livro->isbn ?? 'Não encontrado' }}</p>

                                        <!-- ============================================= -->
                                        <!--     AQUI ESTÁ A ÚNICA ALTERAÇÃO               -->
                                        <!-- ============================================= -->
                                        <div class="card-actions justify-end">
                                            <form method="POST" action="{{ route('livros.importar.store') }}">
                                                @csrf
                                                <input type="hidden" name="titulo" value="{{ $livro->titulo }}">
                                                <input type="hidden" name="autores" value="{{ $livro->autores }}">
                                                <input type="hidden" name="editora" value="{{ $livro->editora }}">
                                                <input type="hidden" name="isbn" value="{{ $livro->isbn }}">
                                                <input type="hidden" name="capa_url" value="{{ $livro->capa_url }}">

                                                @if ($livro->isbn)
                                                    <button type="submit" class="btn btn-sm btn-primary">➕
                                                        Importar</button>
                                                @else
                                                    <button class="btn btn-sm btn-disabled"
                                                        title="Não é possível importar sem ISBN.">Importar</button>
                                                @endif
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-10 text-gray-500">
                                    <p>Nenhum resultado encontrado.</p>
                                </div>
                            @endforelse
                        @else
                            <div class="text-center py-10 text-gray-500">
                                <p>Os resultados da sua pesquisa aparecerão aqui.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
