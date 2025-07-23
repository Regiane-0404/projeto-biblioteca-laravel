<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            ⭐ Deixe a sua Avaliação
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- Card de Informações do Livro -->
            <div class="card card-side bg-base-100 shadow-xl mb-6">
                <figure class="p-4 flex-shrink-0">
                    @php
                        $imageUrl = null;
                        if ($requisicao->livro->imagem_capa) {
                            if (str_starts_with($requisicao->livro->imagem_capa, 'http')) {
                                $imageUrl = $requisicao->livro->imagem_capa;
                            } elseif (Storage::disk('public')->exists($requisicao->livro->imagem_capa)) {
                                $imageUrl = asset('storage/' . $requisicao->livro->imagem_capa);
                            }
                        }
                    @endphp
                    @if ($imageUrl)
                        <img src="{{ $imageUrl }}" alt="Capa" class="w-24 rounded">
                    @else
                        <div class="w-24 h-32 bg-base-200 flex items-center justify-center rounded"><span
                                class="text-4xl opacity-30">📚</span></div>
                    @endif
                </figure>
                <div class="card-body">
                    <h2 class="card-title">{{ $requisicao->livro->nome }}</h2>
                    <p>por {{ $requisicao->livro->autores->pluck('nome')->join(', ') }}</p>
                    <p class="text-sm">Requisitado em: {{ $requisicao->data_inicio->format('d/m/Y') }}</p>
                </div>
            </div>

            <!-- Formulário de Avaliação -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <form action="{{ route('reviews.store', $requisicao) }}" method="POST">
                        @csrf

                        <!-- Classificação por Estrelas -->
                        <div class="form-control mb-4">
                            <label class="label"><span class="label-text font-semibold">Qual a sua
                                    classificação?</span></label>
                            <div class="rating rating-lg">
                                <input type="radio" name="classificacao" value="1"
                                    class="mask mask-star-2 bg-orange-400" required />
                                <input type="radio" name="classificacao" value="2"
                                    class="mask mask-star-2 bg-orange-400" />
                                <input type="radio" name="classificacao" value="3"
                                    class="mask mask-star-2 bg-orange-400" />
                                <input type="radio" name="classificacao" value="4"
                                    class="mask mask-star-2 bg-orange-400" />
                                <input type="radio" name="classificacao" value="5"
                                    class="mask mask-star-2 bg-orange-400" />
                            </div>
                            @error('classificacao')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Comentário -->
                        <div class="form-control mb-6">
                            <label class="label"><span class="label-text font-semibold">Seu comentário
                                    (opcional)</span></label>
                            <textarea name="comentario" class="textarea textarea-bordered h-32" placeholder="O que achou do livro?">{{ old('comentario') }}</textarea>
                            @error('comentario')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Botões de Ação -->
                        <div class="card-actions justify-end">
                            <a href="{{ route('requisicoes.index') }}" class="btn btn-ghost">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Enviar Avaliação</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
