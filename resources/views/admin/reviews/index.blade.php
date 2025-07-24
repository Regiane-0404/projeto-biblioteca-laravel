<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-base-content">
            Avaliações
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div role="alert" class="alert alert-success mb-6"><span>{{ session('success') }}</span></div>
            @endif

            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h3 class="card-title">Avaliações Pendentes de Moderação</h3>
                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <thead>
                                <tr>
                                    <th>Cidadão</th>
                                    <th>Livro</th>
                                    <th>Classificação</th>
                                    <th>Comentário</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($reviewsPendentes as $review)
                                    <tr class="hover">
                                        <td class="font-semibold">{{ $review->user->name ?? 'N/A' }}</td>
                                        <td>{{ $review->livro->nome_visivel ?? 'N/A' }}</td>
                                        <td>
                                            <div class="rating rating-sm">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <input type="radio" name="rating-{{ $review->id }}"
                                                        class="rating-hidden" />
                                                    <!-- A chave é verificar a classificação e aplicar a cor diretamente -->
                                                    <input type="radio" name="rating-{{ $review->id }}"
                                                        class="bg-orange-400 mask mask-star-2"
                                                        {{ $i == $review->classificacao ? 'checked' : '' }} disabled />
                                                @endfor
                                            </div>
                                        </td>
                                        <td class="text-sm max-w-sm">{{ Str::limit($review->comentario, 100) }}</td>
                                        <td>{{ $review->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="flex gap-2">
                                                <!-- Botão Aprovar -->
                                                <form action="{{ route('admin.reviews.aprovar', $review) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-success">✅
                                                        Aprovar</button>
                                                </form>

                                                <!-- Botão Recusar -->
                                                <a href="{{ route('admin.reviews.recusar.form', $review) }}"
                                                    class="btn btn-sm btn-error">
                                                    ❌ Recusar
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-10">
                                            <p class="text-lg text-base-content/60">Não há nenhuma avaliação pendente.
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">
                        {{ $reviewsPendentes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
