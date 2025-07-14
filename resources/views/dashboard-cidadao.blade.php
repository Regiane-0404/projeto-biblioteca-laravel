<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Meu Painel') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Cards de Estatísticas do Cidadão -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="card bg-base-100 shadow">
                    <div class="card-body items-center text-center">
                        <h2 class="card-title text-primary">Requisições Ativas</h2>
                        <p class="text-4xl font-bold">{{ $stats['requisicoes_ativas'] }}</p>
                        <p class="text-xs text-gray-500">Livros que estão consigo agora.</p>
                    </div>
                </div>
                <div class="card bg-base-100 shadow">
                    <div class="card-body items-center text-center">
                        <h2 class="card-title text-secondary">Seu Histórico Total</h2>
                        <p class="text-4xl font-bold">{{ $stats['total_requisicoes'] }}</p>
                        <p class="text-xs text-gray-500">Total de livros já requisitados.</p>
                    </div>
                </div>
                <div class="card bg-base-100 shadow">
                    <div class="card-body items-center text-center">
                        <h2 class="card-title text-accent">Livros Disponíveis</h2>
                        <p class="text-4xl font-bold">{{ $stats['livros_disponiveis'] }}</p>
                        <p class="text-xs text-gray-500">Prontos para serem requisitados.</p>
                    </div>
                </div>
            </div>

            <!-- Últimas Atividades -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h3 class="card-title mb-4">Suas Últimas Requisições</h3>
                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <thead>
                                <tr>
                                    <th>Livro</th>
                                    <th>Data da Requisição</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($requisicoes_recentes as $requisicao)
                                    <tr>
                                        <td class="font-semibold">{{ $requisicao->livro->nome }}</td>
                                        <td>{{ $requisicao->data_inicio->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge badge-sm @if($requisicao->status === 'aprovado') badge-info @elseif($requisicao->status === 'solicitado') badge-warning @else badge-success @endif">
                                                {{ ucfirst($requisicao->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4">
                                            Você ainda não tem requisições. <a href="{{ route('requisicoes.create') }}" class="link link-primary">Que tal requisitar o seu primeiro livro?</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                     @if(count($requisicoes_recentes) > 0)
                        <div class="card-actions justify-end mt-4">
                            <a href="{{ route('requisicoes.index') }}" class="btn btn-outline btn-sm">Ver Todas as Minhas Requisições</a>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>