<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-base-content">📖 Livros Cadastrados</h2>
            @if (auth()->user()->role === 'admin')
                <div class="flex gap-2">
                    <a href="{{ route('livros.create') }}" class="btn btn-primary">➕ Novo Livro</a>
                    <a href="{{ route('livros.exportar', request()->query()) }}" class="btn btn-success">📊 Exportar
                        CSV</a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Mensagens de Feedback --}}
            @if (session('success'))
                <div role="alert" class="alert alert-success mb-6"><span>{{ session('success') }}</span></div>
            @endif
            @if (session('error'))
                <div role="alert" class="alert alert-error mb-6"><span>{{ session('error') }}</span></div>
            @endif
            @if (session('warning'))
                <div role="alert" class="alert alert-warning mb-6"><span>{{ session('warning') }}</span></div>
            @endif

            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <!-- Filtros e Pesquisa -->
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
                                            {{ $editora->nome }}</option>
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

                    <!-- Botões de Filtro de Status -->
                    <div class="flex justify-start gap-2 mb-6 border-t pt-4 mt-4 border-base-300">
                        <a href="{{ route('livros.index', array_merge(request()->query(), ['status' => 'ativo'])) }}"
                            class="btn btn-sm {{ request('status', 'ativo') == 'ativo' ? 'btn-active btn-success' : 'btn-ghost' }}">Ativos</a>
                        <a href="{{ route('livros.index', array_merge(request()->query(), ['status' => 'inativo'])) }}"
                            class="btn btn-sm {{ request('status') == 'inativo' ? 'btn-active btn-warning' : 'btn-ghost' }}">Inativos</a>
                        <a href="{{ route('livros.index', array_merge(request()->query(), ['status' => 'todos'])) }}"
                            class="btn btn-sm {{ request('status') == 'todos' ? 'btn-active' : 'btn-ghost' }}">Todos</a>
                    </div>

                    <!-- Tabela de Livros -->
                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <thead>
                                <tr>
                                    <th>Livro / ISBN</th>
                                    <th>Editora / Autores</th>
                                    <th class="text-center">Estoque</th> <!-- CABEÇALHO ALTERADO -->
                                    <th class="text-center">Status</th>
                                    <th>Ações</th>
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
                                            {{ $livro->editora->nome ?? 'N/A' }}
                                            <br />
                                            <span
                                                class="badge badge-ghost badge-sm">{{ $livro->autores->pluck('nome')->join(', ') ?: 'N/A' }}</span>
                                        </td>

                                        <!-- CÉLULA DE ESTOQUE (SUBSTITUI A DE PREÇO) -->
                                        <td class="text-center font-bold">
                                            <div class="tooltip" data-tip="Exemplares disponíveis">
                                                @if ($livro->quantidade > 5)
                                                    <span class="text-success text-xl">👍
                                                        {{ $livro->quantidade }}</span>
                                                @elseif ($livro->quantidade > 0)
                                                    <span class="text-warning text-xl">🏃‍♂️
                                                        {{ $livro->quantidade }}</span>
                                                @else
                                                    <span class="text-error text-xl">👎 0</span>
                                                @endif
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            @if ($livro->ativo)
                                                <span class="badge badge-success">Ativo</span>
                                            @else
                                                <span class="badge badge-warning">Inativo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('livros.show', $livro) }}"
                                                    class="btn btn-sm btn-outline btn-accent"
                                                    title="Ver Detalhes">👁️</a>
                                                @if (auth()->user()->role === 'admin')
                                                    <a href="{{ route('livros.edit', $livro) }}"
                                                        class="btn btn-sm btn-outline btn-info" title="Editar">✏️</a>
                                                    @if ($livro->ativo)
                                                        <form method="POST"
                                                            action="{{ route('livros.inativar', $livro) }}"
                                                            onsubmit="return confirm('Tem a certeza que deseja INATIVAR este livro?')">
                                                            @csrf @method('PATCH')
                                                            <button type="submit"
                                                                class="btn btn-sm btn-outline btn-warning"
                                                                title="Inativar">⚠️</button>
                                                        </form>
                                                    @else
                                                        <form method="POST"
                                                            action="{{ route('livros.ativar', $livro) }}"
                                                            onsubmit="return confirm('Tem a certeza que deseja ATIVAR este livro?')">
                                                            @csrf @method('PATCH')
                                                            <button type="submit"
                                                                class="btn btn-sm btn-outline btn-success"
                                                                title="Ativar">✅</button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-10">
                                            <p class="text-lg text-base-content/60">Nenhum livro encontrado com os
                                                filtros atuais.</p>
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
