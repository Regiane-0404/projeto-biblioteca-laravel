<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-base-content">
                ➕ Nova Editora
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
                        <div class="text-6xl mb-4">🏢</div>
                        <h3 class="text-xl font-bold">Cadastrar Nova Editora</h3>
                        <p class="text-base-content/60">Preencha as informações da editora</p>
                    </div>

                    <form method="POST" action="{{ route('editoras.store') }}" enctype="multipart/form-data">
                        @csrf

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
                                value="{{ old('nome') }}"
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

                        <!-- Logotipo da Editora -->
                        <div class="form-control mb-6">
                            <label class="label" for="logotipo">
                                <span class="label-text font-semibold text-lg">🎨 Logotipo da Editora</span>
                            </label>
                            
                            <!-- Preview do logotipo -->
                            <div class="flex items-center gap-6 mb-4">
                                <div id="logotipo-preview" class="avatar placeholder">
                                    <div class="bg-neutral text-neutral-content rounded-lg w-20 h-20">
                                        <span class="text-2xl">🏢</span>
                                    </div>
                                </div>
                                <div class="flex-1">
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

                        <!-- Botões -->
                        <div class="flex gap-4 mt-8">
                            <button type="submit" class="btn btn-primary flex-1 btn-lg">
                                ✅ Salvar Editora
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
            } else {
                preview.innerHTML = `
                    <div class="bg-neutral text-neutral-content rounded-lg w-20 h-20">
                        <span class="text-2xl">🏢</span>
                    </div>
                `;
            }
        }
    </script>
</x-app-layout>