<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-base-content">
                ✏️ Editar: {{ $editora->nome }}
            </h2>
            <a href="{{ route('editoras.index') }}" class="btn btn-ghost">
                ← Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Erros de Validação -->
            @if ($errors->any())
                <div class="alert alert-error mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h3 class="font-bold">Erro!</h3>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Formulário -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="text-center mb-6">
                        <div class="text-6xl mb-4">✏️</div>
                        <h3 class="text-xl font-bold">Editar Editora</h3>
                        <p class="text-base-content/60">Atualize as informações da editora</p>
                    </div>

                    <form method="POST" action="{{ route('editoras.update', $editora) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Nome da Editora -->
                        <div class="form-control mb-6">
                            <label class="label" for="nome">
                                <span class="label-text font-semibold text-lg">🏢 Nome da Editora *</span>
                            </label>
                            <input 
                                type="text" 
                                id="nome"
                                name="nome" 
                                class="input input-bordered input-lg w-full @error('nome') input-error @enderror" 
                                value="{{ old('nome', $editora->nome) }}"
                                placeholder="Ex: Editora Saraiva"
                                required
                                autofocus
                            />
                            @error('nome')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Logotipo Atual e Novo -->
                        <div class="form-control mb-6">
                            <label class="label" for="logotipo">
                                <span class="label-text font-semibold text-lg">🎨 Logotipo da Editora</span>
                            </label>
                            
                            <!-- Preview do logotipo atual/novo -->
                            <div class="flex items-center gap-6 mb-4">
                                <div id="logotipo-preview" class="avatar">
                                    @if($editora->logotipo)
                                        <div class="w-20 h-20 rounded-lg overflow-hidden bg-white p-1">
                                            <img src="{{ asset('storage/' . $editora->logotipo) }}" 
                                                 alt="Logotipo atual" 
                                                 class="w-full h-full object-contain">
                                        </div>
                                    @else
                                        <div class="bg-neutral text-neutral-content rounded-lg w-20 h-20">
                                            <span class="text-2xl">🏢</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    @if($editora->logotipo)
                                        <div class="text-sm text-base-content/60 mb-2">
                                            <p><strong>Logotipo atual:</strong> {{ basename($editora->logotipo) }}</p>
                                            <p class="text-xs">Selecione uma nova imagem abaixo para substituir</p>
                                        </div>
                                    @endif
                                    <input 
                                        type="file" 
                                        id="logotipo"
                                        name="logotipo" 
                                        class="file-input file-input-bordered w-full @error('logotipo') file-input-error @enderror"
                                        accept="image/*"
                                        onchange="previewLogotipo(event)"
                                    />
                                    <label class="label">
                                        <span class="label-text-alt">Formatos: JPG, PNG, GIF, SVG (máx. 2MB)</span>
                                    </label>
                                </div>
                            </div>
                            
                            @error('logotipo')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Informações Adicionais -->
                        <div class="bg-base-200 rounded-lg p-4 mb-6">
                            <h4 class="font-semibold mb-2">📊 Informações da Editora</h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-base-content/60">Livros Publicados</p>
                                    <p class="font-semibold">{{ $editora->livros->count() }} {{ $editora->livros->count() === 1 ? 'livro' : 'livros' }}</p>
                                </div>
                                <div>
                                    <p class="text-base-content/60">Cadastrada em</p>
                                    <p class="font-semibold">{{ $editora->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            
                            @if($editora->livros->count() > 0)
                                <div class="mt-3">
                                    <p class="text-base-content/60 text-xs mb-2">Livros desta editora:</p>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($editora->livros->take(3) as $livro)
                                            <div class="badge badge-primary badge-sm">{{ $livro->nome }}</div>
                                        @endforeach
                                        @if($editora->livros->count() > 3)
                                            <div class="badge badge-ghost badge-sm">+{{ $editora->livros->count() - 3 }} mais</div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Botões -->
                        <div class="flex gap-4 mt-8">
                            <button type="submit" class="btn btn-primary flex-1 btn-lg">
                                ✅ Atualizar Editora
                            </button>
                            <a href="{{ route('editoras.index') }}" class="btn btn-ghost btn-lg">
                                ❌ Cancelar
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript para preview do logotipo -->
    <script>
        function previewLogotipo(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('logotipo-preview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <div class="w-20 h-20 rounded-lg overflow-hidden bg-white p-1">
                            <img src="${e.target.result}" alt="Preview" class="w-full h-full object-contain">
                        </div>
                    `;
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
</x-app-layout>