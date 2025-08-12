<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestão de Encomendas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Conteúdo inicial vazio --}}
            <div class="p-6 sm:px-20 bg-white border-b border-gray-200">

            </div>

            {{-- CARD DE FILTROS --}}
            <div class="card bg-base-200 shadow-md mb-6">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.encomendas.index') }}">
                        <div class="flex flex-wrap items-end gap-4">
                            <!-- Filtro Data De -->
                            <div class="form-control">
                                <label class="label"><span class="label-text">Desde:</span></label>
                                <input type="date" name="data_de" value="{{ $filtros['data_de'] ?? '' }}"
                                    class="input input-bordered">
                            </div>
                            <!-- Filtro Data Até -->
                            <div class="form-control">
                                <label class="label"><span class="label-text">Até:</span></label>
                                <input type="date" name="data_ate" value="{{ $filtros['data_ate'] ?? '' }}"
                                    class="input input-bordered">
                            </div>
                            <!-- Filtro por Estado -->
                            <div class="form-control min-w-[200px]">
                                <label class="label"><span class="label-text">Estado:</span></label>
                                <select name="estado" class="select select-bordered w-full">
                                    <option value="">Todos</option>
                                    {{-- Usamos o nosso Enum para gerar as opções dinamicamente --}}
                                    @foreach (App\Enums\EstadoEncomenda::cases() as $estado)
                                        <option value="{{ $estado->value }}"
                                            @if (($filtros['estado'] ?? '') == $estado->value) selected @endif>
                                            {{ ucfirst($estado->value) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Botão Pesquisar -->
                            <div class="form-control">
                                <button type="submit" class="btn btn-primary">Pesquisar</button>
                            </div>
                            <!-- Botão Limpar Filtros -->
                            <div class="form-control">
                                <a href="{{ route('admin.encomendas.index') }}" class="btn btn-ghost">Limpar Filtros</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- O card da tabela começa aqui -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
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
                                                @if ($encomenda->estado->value == 'Enviada') badge-info @endif
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
                        <!-- Paginação (só aparece se a coleção for paginada, ou seja, se houver filtros) -->
                        @if ($encomendas instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            <div class="mt-4">
                                {{ $encomendas->appends($filtros)->links() }}
                            </div>
                        @endif
                    </div>
                </div> {{-- Fim do <div class="p-6"> da tabela --}}
            </div> {{-- Fim do <div class="bg-white ..."> da tabela --}}

            {{-- ======================================================= --}}
            {{-- ==      INÍCIO DA LEGENDA (AGORA NO RODAPÉ)          == --}}
            {{-- ======================================================= --}}
            <div class="mt-6 p-4 bg-base-100 rounded-lg shadow-md">
                <h3 class="font-bold text-lg mb-2">Legenda de Estados:</h3>
                <div class="flex flex-wrap gap-4">
                    <div class="flex items-center">
                        <span class="badge badge-warning mr-2">Pendente</span>
                        <span class="text-sm">- Aguarda confirmação de pagamento.</span>
                    </div>
                    <div class="flex items-center">
                        <span class="badge badge-success mr-2">Pago</span>
                        <span class="text-sm">- Pagamento recebido, pronta para envio.</span>
                    </div>
                    <div class="flex items-center">
                        <span class="badge badge-info mr-2">Enviado</span>
                        <span class="text-sm">- Encomenda postada e a caminho do cliente.</span>
                    </div>
                    <div class="flex items-center">
                        <span class="badge badge-error mr-2">Cancelado</span>
                        <span class="text-sm">- Encomenda cancelada.</span>
                    </div>
                </div>
            </div>
            {{-- ======================================================= --}}
            {{-- ==        FIM DA LEGENDA (AGORA NO RODAPÉ)           == --}}
            {{-- ======================================================= --}}

        </div>
    </div>
</x-app-layout>
