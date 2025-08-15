<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-base-content">
            📖 Gestão de Livros
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">

                    @if (auth()->user()->role === 'admin')
                        <div class="flex justify-end gap-2 mb-6 border-b border-base-300 pb-4">
                            <a href="{{ route('livros.create') }}" class="btn btn-primary">➕ Novo Livro</a>
                            <a href="{{ route('livros.exportar', request()->query()) }}" class="btn btn-outline">📊
                                Exportar CSV</a>
                        </div>
                    @endif

                    <form method="GET" action="{{ route('livros.index') }}" class="mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <div class="form-control md:col-span-2">
                                <label class="label"><span class="label-text">Pesquisar...</span></label>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Título, ISBN, Autor..." class="input input-bordered w-full">
                            </div>
                            <div class="form-control">
                                <label class="label"><span class="label-text">Editora</span></label>
                                <select name="editora" class="select select-bordered w-full"
                                    onchange="this.form.submit()">
                                    <option value="">Todas</option>
                                    @foreach ($editoras as $editora)
                                        <option value="{{ $editora->id }}"
                                            {{ request('editora') == $editora->id ? 'selected' : '' }}>
                                            {{ $editora->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="btn btn-primary flex-grow">🔍 Filtrar</button>
                                <a href="{{ route('livros.index') }}" class="btn btn-ghost"
                                    title="Limpar Filtros">🔄</a>
                            </div>
                        </div>
                    </form>

                    <div class="flex justify-start gap-2 mb-6">
                        <a href="{{ route('livros.index', array_merge(request()->query(), ['status' => 'ativo'])) }}"
                            class="btn btn-sm {{ request('status', 'ativo') == 'ativo' ? 'btn-active btn-success' : 'btn-ghost' }}">Ativos</a>
                        <a href="{{ route('livros.index', array_merge(request()->query(), ['status' => 'inativo'])) }}"
                            class="btn btn-sm {{ request('status') == 'inativo' ? 'btn-active btn-warning' : 'btn-ghost' }}">Inativos</a>
                        <a href="{{ route('livros.index', array_merge(request()->query(), ['status' => 'todos'])) }}"
                            class="btn btn-sm {{ request('status') == 'todos' ? 'btn-active' : 'btn-ghost' }}">Todos</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <thead>
                                <tr>
                                    <th>Livro / ISBN</th>
                                    <th>Editora / Autores</th>
                                    <th class="text-center">Estoque</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($livros as $livro)
                                    <tr class="hover">
                                        <td>
                                            <div class="flex items-center gap-3">
                                                @php
                                                    $imageUrl = null;
                                                    if ($livro->imagem_capa) {
                                                        if (str_starts_with($livro->imagem_capa, 'http')) {
                                                            $imageUrl = $livro->imagem_capa;
                                                        } elseif (
                                                            Storage::disk('public')->exists($livro->imagem_capa)
                                                        ) {
                                                            $imageUrl = asset('storage/' . $livro->imagem_capa);
                                                        }
                                                    }
                                                @endphp
                                                <div class="avatar">
                                                    <div class="mask mask-squircle w-12 h-12 bg-base-200">
                                                        @if ($imageUrl)
                                                            <img src="{{ $imageUrl }}"
                                                                alt="Capa de {{ $livro->nome }}">
                                                        @else
                                                            <span
                                                                class="text-xl opacity-40 flex items-center justify-center w-full h-full">📚</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-bold">{{ $livro->nome }}</div>
                                                    <div class="text-sm opacity-60 font-mono">{{ $livro->isbn }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ $livro->editora->nome ?? 'N/A' }}<br />
                                            <span
                                                class="badge badge-ghost badge-sm">{{ $livro->autores->pluck('nome')->join(', ') ?: 'N/A' }}</span>
                                        </td>

                                        {{-- Bloco CORRIGIDO com Estilo "Outline" --}}
                                        <td class="text-center">
                                            @if ($livro->quantidade > 3)
                                                <div class="badge badge-success badge-outline">{{ $livro->quantidade }}
                                                    Disponíveis</div>
                                            @elseif ($livro->quantidade > 0)
                                                <div class="badge badge-warning badge-outline">Últimas Unidades
                                                    ({{ $livro->quantidade }})
                                                </div>
                                            @else
                                                <div class="badge badge-outline">Indisponível</div>
                                            @endif
                                        </td>

                                        {{-- Badge de Status ajustada visualmente --}}
                                        <td class="text-center">
                                            @if ($livro->ativo)
                                                <span
                                                    class="badge badge-outline text-success border-success">Ativo</span>
                                            @else
                                                <span
                                                    class="badge badge-outline text-warning border-warning">Inativo</span>
                                            @endif
                                        </td>

                                        <td class="text-right">
                                            <div class="inline-block">
                                                <a href="{{ route('livros.show', $livro) }}"
                                                    class="btn btn-ghost btn-sm" title="Ver Detalhes">Ver</a>

                                                @if (auth()->user()->role === 'admin')
                                                    <div class="dropdown dropdown-end">
                                                        <div tabindex="0" role="button" class="btn btn-ghost btn-sm"
                                                            title="Mais Ações">
                                                            <svg xmlns="http://www.w.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24"
                                                                class="inline-block w-5 h-5 stroke-current">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M5 12h.01M12 12h.01M19 12h.01" />
                                                            </svg>
                                                        </div>
                                                        <ul
                                                            class="dropdown-content menu p-2 shadow-lg bg-base-200 rounded-box w-52 z-[1] mt-2">
                                                            <li><a href="{{ route('livros.edit', $livro) }}">✏️ Editar
                                                                    Livro</a></li>
                                                            <div class="divider my-1"></div>
                                                            @if ($livro->ativo)
                                                                <li>
                                                                    <form class="w-full" method="POST"
                                                                        action="{{ route('livros.inativar', $livro) }}"
                                                                        onsubmit="return confirm('Tem a certeza?')">
                                                                        <button type="submit"
                                                                            class="w-full text-left p-2 rounded-lg hover:bg-base-200">⚠️
                                                                            Inativar</button>
                                                                        @csrf @method('PATCH')
                                                                    </form>
                                                                </li>
                                                            @else
                                                                <li>
                                                                    <form class="w-full" method="POST"
                                                                        action="{{ route('livros.ativar', $livro) }}"
                                                                        onsubmit="return confirm('Tem a certeza?')">
                                                                        <button type="submit"
                                                                            class="w-full text-left p-2 rounded-lg hover:bg-base-200">✅
                                                                            Ativar</button>
                                                                        @csrf @method('PATCH')
                                                                    </form>
                                                                </li>
                                                            @endif
                                                            <li
                                                                @if (!$livro->podeSerExcluido()) class="tooltip tooltip-left" data-tip="Este livro tem requisições." @endif>
                                                                <form class="w-full" method="POST"
                                                                    action="{{ route('livros.destroy', $livro) }}"
                                                                    onsubmit="return confirm('AÇÃO IRREVERSÍVEL! Tem a certeza?')">
                                                                    <button type="submit"
                                                                        class="w-full text-left p-2 rounded-lg hover:bg-error hover:text-error-content"
                                                                        @if (!$livro->podeSerExcluido()) disabled @endif>❌
                                                                        Excluir</button>
                                                                    @csrf @method('DELETE')
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-10">
                                            <p class="text-lg text-base-content/60">Nenhum livro encontrado.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $livros->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
