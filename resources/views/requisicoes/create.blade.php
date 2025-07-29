<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            ➕ Nova Requisição
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensagens de Erro -->
            @if (session('error'))
                <div class="alert alert-error mb-6">
                    <span>❌ {{ session('error') }}</span>
                </div>
            @endif

            <!-- Pesquisa -->
            <div class="card bg-base-100 shadow-lg mb-6">
                <div class="card-body">
                    <form method="GET" action="{{ route('requisicoes.create') }}">
                        <div class="flex gap-4">
                            <div class="form-control flex-1">
                                <input type="text" name="search" value="{{ $search }}"
                                    placeholder="🔍 Pesquisar por título, autor ou editora..."
                                    class="input input-bordered w-full">
                            </div>
                            <button type="submit" class="btn btn-primary">
                                Pesquisar
                            </button>
                            @if ($search)
                                <a href="{{ route('requisicoes.create') }}" class="btn btn-outline">
                                    Limpar
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Formulário -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h3 class="card-title text-2xl mb-6">📚 Selecionar Livros</h3>

                    <form action="{{ route('requisicoes.store') }}" method="POST">
                        @csrf

                        <div class="form-control mb-6">
                            <label class="label">
                                <span class="label-text font-semibold">
                                    Escolha até 3 livros ({{ $livrosDisponiveis->total() }} disponíveis):
                                </span>
                            </label>

                            @if ($livrosDisponiveis->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                                    @foreach ($livrosDisponiveis as $livro)
                                        <label class="cursor-pointer">
                                            <div
                                                class="card card-compact bg-base-100 border-2 border-transparent hover:border-primary transition-colors">
                                                <div class="card-body">
                                                    <div class="flex items-start gap-3">
                                                        <input type="checkbox" name="livros_ids[]"
                                                            value="{{ $livro->id }}"
                                                            class="checkbox checkbox-primary mt-1"
                                                            onchange="limitarSelecao(this)"
                                                            @if (isset($livro_id) && $livro->id == $livro_id) checked
                                                                disabled @endif>
                                                        <div class="flex-1">
                                                            <h3 class="font-semibold text-sm">{{ $livro->nome }}</h3>
                                                            <p class="text-xs text-gray-600">{{ $livro->editora->nome }}
                                                            </p>
                                                            @if ($livro->autores->count() > 0)
                                                                <p class="text-xs text-gray-500">
                                                                    Por:
                                                                    {{ $livro->autores->pluck('nome')->join(', ') }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                <div class="mb-4">
                                    {{ $livrosDisponiveis->links() }}
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <span>⚠️ Nenhum livro encontrado para "{{ $search }}"</span>
                                </div>
                            @endif

                            <div class="mt-2">
                                <span id="contador" class="text-sm text-gray-600">0/3 livros selecionados</span>
                            </div>

                            @error('livros_ids')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <div class="alert alert-info mb-6">
                            <span>ℹ️ <strong>Informações importantes:</strong></span>
                            <ul class="list-disc list-inside mt-2">
                                <li>Você pode selecionar até 3 livros em simultâneo</li>
                                <li>Prazo de devolução: 5 dias úteis</li>
                                <li>A requisição deve ser aprovada por um administrador</li>
                            </ul>
                        </div>

                        <div class="card-actions justify-end">
                            <a href="{{ route('requisicoes.index') }}" class="btn btn-outline">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary" id="btnSubmit" disabled>
                                ➕ Criar Requisição
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function limitarSelecao(checkbox) {
        const checkboxes = document.querySelectorAll('input[name="livros_ids[]"]');
        const selecionados = Array.from(checkboxes).filter(cb => cb.checked);
        const contador = document.getElementById('contador');
        const btnSubmit = document.getElementById('btnSubmit');

        contador.innerHTML = `${selecionados.length}/3 livros selecionados`;

        if (selecionados.length > 0) {
            if (!document.getElementById('btnLimpar')) {
                const limparBtn = document.createElement('button');
                limparBtn.type = 'button';
                limparBtn.id = 'btnLimpar';
                limparBtn.className = 'btn btn-sm btn-outline btn-warning ml-2';
                limparBtn.innerHTML = '🗑️ Limpar Seleção';
                limparBtn.onclick = limparSelecao;
                contador.appendChild(limparBtn);
            }
        } else {
            const btnLimpar = document.getElementById('btnLimpar');
            if (btnLimpar) {
                btnLimpar.remove();
            }
        }

        if (selecionados.length >= 3) {
            checkboxes.forEach(cb => {
                if (!cb.checked && !cb.disabled) {
                    cb.disabled = true;
                }
            });
            contador.classList.add('text-warning');
        } else {
            checkboxes.forEach(cb => {
                if (!cb.hasAttribute('data-fixed')) {
                    cb.disabled = false;
                }
            });
            contador.classList.remove('text-warning');
        }

        btnSubmit.disabled = selecionados.length === 0;
    }

    function limparSelecao() {
        const checkboxes = document.querySelectorAll('input[name="livros_ids[]"]');
        checkboxes.forEach(cb => {
            if (!cb.hasAttribute('disabled') || !cb.checked) {
                cb.checked = false;
                cb.disabled = false;
            }
        });

        const contador = document.getElementById('contador');
        contador.innerHTML = '0/3 livros selecionados';
        contador.classList.remove('text-warning');

        const btnLimpar = document.getElementById('btnLimpar');
        if (btnLimpar) {
            btnLimpar.remove();
        }

        document.getElementById('btnSubmit').disabled = true;
    }
</script>
