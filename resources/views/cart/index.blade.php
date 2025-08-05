<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-base-content">
            🛒 Seu Carrinho de Compras
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div role="alert" class="alert alert-success mb-6 shadow-md">
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div role="alert" class="alert alert-error mb-6 shadow-md">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    @if ($cartItems && $cartItems->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="table w-full">
                                <thead>
                                    <tr>
                                        <th>Livro</th>
                                        <th class="text-center">Quantidade</th>
                                        <th class="text-right">Preço Unit.</th>
                                        <th class="text-right">Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cartItems as $item)
                                        @if ($item->livro)
                                            <tr>
                                                <td>
                                                    <div class="flex items-center space-x-3">
                                                        @php
                                                            $imageUrl = null;
                                                            if ($item->livro->imagem_capa) {
                                                                if (
                                                                    str_starts_with($item->livro->imagem_capa, 'http')
                                                                ) {
                                                                    $imageUrl = $item->livro->imagem_capa;
                                                                } elseif (
                                                                    Storage::disk('public')->exists(
                                                                        $item->livro->imagem_capa,
                                                                    )
                                                                ) {
                                                                    $imageUrl = asset(
                                                                        'storage/' . $item->livro->imagem_capa,
                                                                    );
                                                                }
                                                            }
                                                        @endphp
                                                        <div class="avatar">
                                                            <div class="mask mask-squircle w-12 h-12 bg-base-200">
                                                                @if ($imageUrl)
                                                                    <img src="{{ $imageUrl }}"
                                                                        alt="Capa de {{ $item->livro->nome }}" />
                                                                @else
                                                                    <span
                                                                        class="text-xl opacity-40 flex items-center justify-center w-full h-full">📚</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="font-bold">{{ $item->livro->nome }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ $item->quantity }}</td>
                                                <td class="text-right">
                                                    €{{ number_format((float) $item->livro->preco, 2, ',', '.') }}
                                                </td>
                                                <td class="text-right">
                                                    €{{ number_format((float) $item->livro->preco * $item->quantity, 2, ',', '.') }}
                                                </td>
                                                <td class="text-right">
                                                    <form action="{{ route('cart.remove', $item->id) }}" method="POST"
                                                        onsubmit="return confirm('Tem a certeza que deseja remover este item?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-ghost btn-sm text-error"
                                                            title="Remover">
                                                            ❌
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="divider"></div>
                        <div class="text-right">
                            <p class="text-lg font-bold">Total:
                                <span class="text-primary">
                                    €{{ number_format($total, 2, ',', '.') }}
                                </span>
                            </p>
                            <p class="text-xs text-base-content/60">Taxas e envio calculados no próximo passo.</p>
                        </div>
                        <div class="card-actions justify-end mt-6">
                            <a href="{{ route('home') }}" class="btn btn-ghost">Continuar a Comprar</a>
                            <a href="#" class="btn btn-primary">Finalizar Compra</a>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-lg text-base-content/60">O seu carrinho está vazio.</p>
                            <a href="{{ route('home') }}" class="btn btn-primary mt-4">Explorar Livros</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
