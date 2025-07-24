<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-base-content">
            ❌ Recusar Avaliação
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <p class="mb-4">Você está a recusar a avaliação para o livro
                        <strong>{{ $review->livro->nome }}</strong> feita por
                        <strong>{{ $review->user->name }}</strong>.</p>

                    <form action="{{ route('admin.reviews.recusar.submit', $review) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="form-control">
                            <label class="label"><span class="label-text">Por favor, escreva uma justificação clara
                                    para a recusa:</span></label>
                            <textarea name="justificacao_recusa" class="textarea textarea-bordered h-32" required>{{ old('justificacao_recusa') }}</textarea>
                            @error('justificacao_recusa')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="card-actions justify-end mt-6">
                            <a href="{{ route('admin.reviews.index') }}" class="btn btn-ghost">Cancelar</a>
                            <button type="submit" class="btn btn-error">Confirmar Recusa</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
