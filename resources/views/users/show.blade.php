<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                👤 Detalhes do Usuário
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Card de Informações do Usuário -->
            <div class="card bg-base-100 shadow-xl mb-6">
                <div class="card-body">
                    <div class="flex justify-between items-start">
                        <!-- Informações do Usuário à Esquerda -->
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
                        <!-- Pontuação à Direita -->
                        <div class="text-right">
                            <div class="stat-title">Pontuação</div>
                            <div class="stat-value text-primary">🏆 {{ $user->pontos }}</div>
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
                                        } elseif ($requisicao->status === 'cancelado') {
                                            $statusClass = 'text-gray-400';
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
                                            @elseif($requisicao->status === 'devolvido')
                                                Devolvido
                                            @elseif($requisicao->status === 'cancelado')
                                                Cancelado
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
                </div>
            </div>

            <!-- NOVO LOCAL PARA O BOTÃO VOLTAR -->
            <div class="mt-6 flex justify-end">
                <a href="{{ route('users.index') }}" class="btn btn-outline">⬅️ Voltar para a Lista de Usuários</a>
            </div>

        </div>
    </div>
</x-app-layout>
