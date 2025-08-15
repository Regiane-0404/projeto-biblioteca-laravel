<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📜 Logs de Atividade do Sistema
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- CARD DE FILTROS -->
            <div class="card bg-base-200 shadow-md mb-6">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.logs.index') }}">
                        <div class="flex flex-wrap items-end gap-4">
                            <!-- Filtros -->
                            <div class="form-control">
                                <label class="label"><span class="label-text">Desde:</span></label>
                                <input type="date" name="data_de" value="{{ $filtros['data_de'] ?? '' }}"
                                    class="input input-bordered">
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Até:</span></label>
                                <input type="date" name="data_ate" value="{{ $filtros['data_ate'] ?? '' }}"
                                    class="input input-bordered">
                            </div>
                            <div class="form-control min-w-[200px]">
                                <label class="label"><span class="label-text">Módulo:</span></label>
                                <select name="log_name" class="select select-bordered w-full">
                                    <option value="">Todos</option>
                                    @foreach ($modulos as $modulo)
                                        <option value="{{ $modulo }}"
                                            @if (($filtros['log_name'] ?? '') == $modulo) selected @endif>
                                            {{ $modulo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Botões -->
                            <div class="form-control">
                                <button type="submit" class="btn btn-primary">Pesquisar</button>
                            </div>
                            <div class="form-control">
                                <a href="{{ route('admin.logs.index') }}" class="btn btn-ghost">Limpar</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Card da Tabela -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="table w-full table-zebra">
                            <thead>
                                <tr>
                                    <th>Data / Hora</th>
                                    <th>Utilizador</th>
                                    <th>Módulo</th>
                                    <th>Descrição da Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Usamos um @forelse para lidar com o caso de não haver logs --}}
                                @forelse ($logs as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                        <td>
                                            {{-- Verificamos se 'causer' não é nulo antes de aceder ao nome --}}
                                            @if ($log->causer)
                                                {{ $log->causer->name }}
                                                <span class="text-xs opacity-60"> (ID: {{ $log->causer_id }})</span>
                                            @else
                                                <span class="italic opacity-60">Sistema / Anónimo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-ghost">{{ $log->log_name }}</span>
                                        </td>
                                        <td>
                                            {{ $log->description }}
                                            @if ($log->subject)
                                                <span class="text-xs opacity-60">
                                                    (Objeto: {{ class_basename($log->subject_type) }}
                                                    #{{ $log->subject_id }})
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <p>Nenhum registo de atividade encontrado com os filtros atuais.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>


                    <!-- Paginação (só aparece se a coleção for paginada, ou seja, se houver filtros) -->
                    @if ($logs instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="mt-4">
                            {{ $logs->appends($filtros)->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
