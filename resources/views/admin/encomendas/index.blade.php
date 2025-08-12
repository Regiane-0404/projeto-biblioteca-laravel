<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestão de Encomendas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">

                </div>

                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="table w-full table-zebra">
                            <!-- Cabeçalho da Tabela -->
                            <thead>
                                <tr>
                                    <th>Nº Encomenda</th>
                                    <th>Cliente</th>
                                    <th>Data</th>
                                    <th>Estado</th>
                                    <th class="text-right">Total</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($encomendas as $encomenda)
                                    <tr>
                                        <!-- Número da Encomenda -->
                                        <td>{{ $encomenda->numero_encomenda }}</td>
                                        <!-- Nome do Cliente (da relação 'user') -->
                                        <td>{{ $encomenda->user->name ?? 'Utilizador Removido' }}</td>
                                        <!-- Data formatada -->
                                        <td>{{ $encomenda->created_at->format('d/m/Y H:i') }}</td>
                                        <!-- Estado com um badge colorido -->
                                        <td>
                                            <span
                                                class="badge
                                                @if ($encomenda->estado->value == 'Pendente') badge-warning @endif
                                                @if ($encomenda->estado->value == 'Paga') badge-success @endif
                                                @if ($encomenda->estado->value == 'Cancelada') badge-error @endif
                                            ">
                                                {{ $encomenda->estado->value }}
                                            </span>
                                        </td>
                                        <!-- Total formatado como moeda -->
                                        <td class="text-right">€ {{ number_format($encomenda->total, 2, ',', '.') }}
                                        </td>
                                        <!-- Botão de Ações (para o futuro) -->
                                        <td class="text-center">
                                            <a href="{{ route('admin.encomendas.show', ['encomenda' => $encomenda->id]) }}"
                                                class="text-blue-600 hover:underline">Ver</a>

                                        </td>
                                    </tr>
                                @empty
                                    <!-- Mensagem para quando não há encomendas -->
                                    <tr>
                                        <td colspan="6" class="text-center py-4">Nenhuma encomenda encontrada.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Links de Paginação -->
                    <div class="mt-4">
                        {{ $encomendas->links() }}
                    </div>

                    {{-- =============================================== --}}
                    {{-- ==     INÍCIO DO NOVO BLOCO DE LEGENDA       == --}}
                    {{-- =============================================== --}}
                    <div class="mt-10 p-4 bg-base-200 rounded-lg">
                        <h3 class="font-bold text-lg mb-2">Legenda de Estados:</h3>
                        <div class="flex flex-wrap gap-4">
                            <div class="flex items-center">
                                <span class="badge badge-warning mr-2">Pendente</span>
                                <span class="text-sm">- Aguarda confirmação de pagamento.</span>
                            </div>
                            <div class="flex items-center">
                                <span class="badge badge-success mr-2">Paga</span>
                                <span class="text-sm">- Pagamento recebido, pronta para envio.</span>
                            </div>
                            <div class="flex items-center">
                                <span class="badge badge-info mr-2">Enviada</span>
                                <span class="text-sm">- Encomenda postada e a caminho do cliente.</span>
                            </div>
                            <div class="flex items-center">
                                <span class="badge badge-error mr-2">Cancelada</span>
                                <span class="text-sm">- Encomenda cancelada.</span>
                            </div>
                        </div>
                    </div>
                    {{-- =============================================== --}}
                    {{-- ==       FIM DO NOVO BLOCO DE LEGENDA        == --}}
                    {{-- =============================================== --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
