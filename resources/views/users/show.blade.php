<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                👤 Detalhes do Usuário
            </h2>
            <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline">⬅️ Voltar para a Lista</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Card de Informações do Usuário -->
            <div class="card bg-base-100 shadow-xl mb-6">
                <div class="card-body">
                    <div class="flex items-center gap-4">
                        <div class="avatar">
                            <div class="w-16 rounded-full">
                                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" />
                            </div>
                        </div>
                        <div>
                            <h2 class="card-title text-2xl font-bold">{{ $user->name }}</h2>
                            <p class="text-gray-600">{{ $user->email }}</p>
                            <div class="mt-2 flex gap-2">
                                @if ($user->role === 'admin')
                                    <span class="badge badge-error text-white">👑 Administrador</span>
                                @else
                                    <span class="badge badge-info text-white">👤 Cidadão</span>
                                @endif
                                @if ($user->ativo)
                                    <span class="badge badge-success text-white">✅ Ativo</span>
                                @else
                                    <span class="badge badge-warning text-white">⚠️ Inativo</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card com Histórico de Requisições do Usuário -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h3 class="card-title text-2xl mb-4">📜 Histórico de Requisições</h3>
                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <thead>
                                <tr>
                                    <th>Livro Requisitado</th>
                                    <th>Data da Requisição</th>
                                    <th>Data de Devolução (Prevista)</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($user->requisicoes as $requisicao)
                                    @php
                                        // Lógica para determinar a cor do texto do status
                                        $isAtrasado =
                                            now()->isAfter($requisicao->data_fim_prevista) &&
                                            $requisicao->status === 'aprovado';
                                        $statusClass = '';
                                        if ($isAtrasado) {
                                            $statusClass = 'text-error font-bold';
                                        } elseif ($requisicao->status === 'devolvido') {
                                            $statusClass = 'text-success';
                                        } elseif ($requisicao->status === 'aprovado') {
                                            $statusClass = 'text-info';
                                        } elseif ($requisicao->status === 'solicitado') {
                                            $statusClass = 'text-warning';
                                        }
                                    @endphp
                                    <tr class="hover">
                                        <td class="font-semibold">
                                            {{ $requisicao->livro->nome_visivel ?? 'Livro Removido' }}</td>
                                        <td>{{ $requisicao->data_inicio->format('d/m/Y') }}</td>
                                        <td>{{ $requisicao->data_fim_prevista->format('d/m/Y') }}</td>
                                        <td class="text-center font-semibold {{ $statusClass }}">
                                            @if ($isAtrasado)
                                                Atrasado
                                            @elseif($requisicao->status === 'aprovado')
                                                Em posse
                                            @else
                                                {{ ucfirst($requisicao->status) }}
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-8">
                                            <p class="text-gray-500">Este usuário nunca fez uma requisição.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Legenda -->
                    <div class="mt-6 border-t pt-4 text-sm text-gray-600">
                        <h4 class="font-semibold mb-2">Legenda de Status:</h4>
                        <div class="flex flex-wrap gap-x-4 gap-y-2">
                            <span class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-warning mr-2"></div> Solicitado
                            </span>
                            <span class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-info mr-2"></div> Em posse
                            </span>
                            <span class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-success mr-2"></div> Devolvido
                            </span>
                            <span class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-error mr-2"></div> Atrasado
                            </span>
                            <span class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-gray-300 mr-2"></div> Cancelado
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
