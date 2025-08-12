<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalhes da Encomenda #{{ $encomenda->numero_encomenda }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">

                <!-- CARD 1: Resumo Geral -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title">Resumo da Encomenda</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="stat p-0">
                                <div class="stat-title">Nº da Encomenda</div>
                                <div class="stat-value text-base">{{ $encomenda->numero_encomenda }}</div>
                            </div>
                            <div class="stat p-0">
                                <div class="stat-title">Data</div>
                                <div class="stat-value text-base">{{ $encomenda->created_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            <div class="stat p-0">
                                <div class="stat-title">Estado Atual</div>
                                <div class="stat-value text-base">
                                    <span
                                        class="badge badge-lg
                                        @if ($encomenda->estado->value == 'Pendente') badge-warning @endif
                                        @if ($encomenda->estado->value == 'Paga') badge-success @endif
                                        @if ($encomenda->estado->value == 'Cancelada') badge-error @endif
                                        @if ($encomenda->estado->value == 'Enviada') badge-info @endif
                                    ">
                                        {{ $encomenda->estado->value }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: Itens e Totais -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title">Itens e Cálculo de Valores</h3>
                        <div class="overflow-x-auto">
                            {{-- CORREÇÃO: Tabela com mais padding e imagem do livro corrigida --}}
                            <table class="table w-full">
                                <thead>
                                    <tr>
                                        <th>Livro</th>
                                        <th class="text-center">Qtd.</th>
                                        <th class="text-right">Preço Unit.</th>
                                        <th class="text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($encomenda->itens as $item)
                                        <tr class="hover">
                                            <td>
                                                <div class="flex items-center space-x-4">
                                                    <div class="avatar">
                                                        {{-- REMOVIDO mask-squircle para evitar cortes --}}
                                                        <div class="w-16 rounded">
                                                            <img src="{{ $item->livro->url_capa }}"
                                                                alt="Capa de {{ $item->livro->nome }}" />
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="font-bold">{{ $item->livro->nome }}</div>
                                                        <div class="text-sm opacity-50">SKU:
                                                            {{ $item->livro->sku ?? 'N/D' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">{{ $item->quantidade }}</td>
                                            <td class="text-right">€ {{ number_format($item->preco, 2, ',', '.') }}</td>
                                            <td class="text-right font-bold">€
                                                {{ number_format($item->quantidade * $item->preco, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{-- Divisor e Totais --}}
                        <div class="divider"></div>
                        <div class="w-full md:w-1/2 md:self-end">
                            <div class="space-y-2">
                                <div class="flex justify-between"><span>Subtotal:</span> <span>€
                                        {{ number_format($encomenda->subtotal, 2, ',', '.') }}</span></div>
                                <div class="flex justify-between"><span>Portes de Envio:</span> <span>€
                                        {{ number_format($encomenda->portes_envio, 2, ',', '.') }}</span></div>
                                <div class="flex justify-between"><span>Impostos (IVA):</span> <span>€
                                        {{ number_format($encomenda->impostos, 2, ',', '.') }}</span></div>
                                <div class="flex justify-between text-xl font-bold text-primary pt-2 border-t">
                                    <span>TOTAL:</span> <span>€
                                        {{ number_format($encomenda->total, 2, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: Cliente e Morada -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title">Dados do Cliente e Entrega</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-bold mb-2">Cliente</h4>
                                <p>{{ $encomenda->user->name }}</p>
                                <p class="text-sm opacity-70">{{ $encomenda->user->email }}</p>
                            </div>
                            <div>
                                <h4 class="font-bold mb-2">Morada de Entrega</h4>
                                <address class="not-italic">
                                    {{ $encomenda->moradaEnvio->nome_completo }}<br>
                                    {{ $encomenda->moradaEnvio->morada }}<br>
                                    @if ($encomenda->moradaEnvio->complemento)
                                        {{ $encomenda->moradaEnvio->complemento }}<br>
                                    @endif
                                    {{ $encomenda->moradaEnvio->codigo_postal }}
                                    {{ $encomenda->moradaEnvio->localidade }}<br>
                                    {{ $encomenda->moradaEnvio->pais }}
                                </address>
                                @if ($encomenda->moradaEnvio->nif)
                                    <p class="mt-2"><strong>NIF:</strong> {{ $encomenda->moradaEnvio->nif }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 4: Ações (Rodapé) -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title">Ações</h3>
                        <div class="card-actions justify-between items-center">
                            {{-- Botões de Ação à Esquerda --}}
                            <div class="flex gap-2">

                                {{-- AÇÃO: Marcar como Pago (só aparece se a encomenda estiver PENDENTE) --}}
                                @if ($encomenda->estado->value === 'pendente')
                                    <form action="{{ route('admin.encomendas.marcar.pago', $encomenda) }}"
                                        method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success">Marcar como Pago
                                            (Manual)</button>
                                    </form>
                                @endif

                                {{-- AÇÃO: Marcar como Enviada (só aparece se a encomenda estiver PAGA) --}}
                                @if ($encomenda->estado->value === 'pago')
                                    <form action="{{ route('admin.encomendas.marcar.enviada', $encomenda) }}"
                                        method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-info">Marcar como Enviada</button>
                                    </form>
                                @endif

                                {{-- Lógica para o botão de cancelar (só aparece se não estiver já enviada ou cancelada) --}}
                                @if (!in_array($encomenda->estado->value, ['enviado', 'cancelado']))
                                    <form action="{{ route('admin.encomendas.cancelar', $encomenda) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-error">Cancelar Encomenda</button>
                                    </form>
                                @endif
                                {{-- ======================================================= --}}
                                {{-- ==           BOTÃO DE FATURA (NOVA POSIÇÃO)          == --}}
                                {{-- ======================================================= --}}
                                {{-- Só aparece se a encomenda estiver PAGA, ENVIADA ou ENTREGUE --}}
                                @if (in_array($encomenda->estado->value, ['pago', 'enviado', 'entregue']))
                                    <a href="{{ route('admin.encomendas.fatura.pdf', $encomenda) }}" target="_blank"
                                        class="btn btn-secondary">
                                        📄 Gerar Fatura
                                    </a>
                                @endif
                            </div>
                            {{-- Botão Voltar à Direita --}}
                            <a href="{{ route('admin.encomendas.index') }}" class="btn btn-ghost">
                                ← Voltar para a Listagem
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
