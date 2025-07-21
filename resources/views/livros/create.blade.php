<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-base-content">
                ➕ Novo Livro
            </h2>
            <a href="{{ route('livros.index') }}" class="btn btn-ghost">
                ← Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Erros de Validação -->
            @if ($errors->any())
                <div class="alert alert-error mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                    <form method="POST" action="{{ route('livros.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                            <!-- Coluna Esquerda -->
                            <div class="space-y-4">

                                <!-- Nome do Livro -->
                                <div class="form-control">
                                    <label class="label" for="nome">
                                        <span class="label-text font-semibold">📖 Nome do Livro *</span>
                                    </label>
                                    <input type="text" id="nome" name="nome"
                                        class="input input-bordered w-full @error('nome') input-error @enderror"
                                        value="{{ old('nome') }}" placeholder="Ex: Dom Casmurro" required />
                                    @error('nome')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <!-- ISBN -->
                                <div class="form-control">
                                    <label class="label" for="isbn">
                                        <span class="label-text font-semibold">🏷️ ISBN *</span>
                                    </label>
                                    <input type="text" id="isbn" name="isbn"
                                        class="input input-bordered w-full @error('isbn') input-error @enderror"
                                        value="{{ old('isbn') }}" placeholder="Ex: 978-85-250-0000-0" required />
                                    @error('isbn')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <!-- Editora -->
                                <div class="form-control">
                                    <label class="label" for="editora_id">
                                        <span class="label-text font-semibold">🏢 Editora *</span>
                                    </label>
                                    <select id="editora_id" name="editora_id"
                                        class="select select-bordered w-full @error('editora_id') select-error @enderror"
                                        required>
                                        <option value="">Selecione uma editora</option>
                                        @foreach ($editoras as $editora)
                                            <option value="{{ $editora->id }}"
                                                {{ old('editora_id') == $editora->id ? 'selected' : '' }}>
                                                {{ $editora->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('editora_id')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <!-- Preço -->
                                <div class="form-control">
                                    <label class="label" for="preco">
                                        <span class="label-text font-semibold">💰 Preço (€) *</span>
                                    </label>
                                    <input type="number" id="preco" name="preco"
                                        class="input input-bordered w-full @error('preco') input-error @enderror"
                                        value="{{ old('preco') }}" placeholder="Ex: 15.90" step="0.01"
                                        min="0" required />
                                    @error('preco')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <!-- ============================================= -->
                                <!--   NOVO CAMPO PARA QUANTIDADE EM ESTOQUE       -->
                                <!-- ============================================= -->
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Quantidade em Estoque</span>
                                    </label>
                                    <input type="number" name="quantidade" value="{{ old('quantidade', 1) }}"
                                        placeholder="Ex: 5"
                                        class="input input-bordered @error('quantidade') input-error @enderror"
                                        min="0" required>
                                    @error('quantidade')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                            </div>

                            <!-- Coluna Direita -->
                            <div class="space-y-4">

                                <!-- Autores -->
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">✍️ Autores *</span>
                                    </label>
                                    <div class="grid grid-cols-1 gap-2 max-h-48 overflow-y-auto border rounded-lg p-3">
                                        @foreach ($autores as $autor)
                                            <label class="cursor-pointer label justify-start gap-3">
                                                <input type="checkbox" name="autores[]" value="{{ $autor->id }}"
                                                    class="checkbox checkbox-primary"
                                                    {{ in_array($autor->id, old('autores', [])) ? 'checked' : '' }} />
                                                <span class="label-text">{{ $autor->nome }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('autores')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                <!-- Capa do Livro -->
                                <div class="form-control">
                                    <label class="label" for="imagem_capa">
                                        <span class="label-text font-semibold">🖼️ Capa do Livro</span>
                                    </label>
                                    <input type="file" id="imagem_capa" name="imagem_capa"
                                        class="file-input file-input-bordered w-full @error('imagem_capa') file-input-error @enderror"
                                        accept="image/*" />
                                    <label class="label">
                                        <span class="label-text-alt">Formatos: JPG, PNG, GIF (máx. 2MB)</span>
                                    </label>
                                    @error('imagem_capa')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                            </div>
                        </div>

                        <!-- Bibliografia (linha completa) -->
                        <div class="form-control mt-6">
                            <label class="label" for="bibliografia">
                                <span class="label-text font-semibold">📄 Bibliografia/Descrição</span>
                            </label>
                            <textarea id="bibliografia" name="bibliografia"
                                class="textarea textarea-bordered h-24 @error('bibliografia') textarea-error @enderror"
                                placeholder="Breve descrição do livro, sinopse, etc...">{{ old('bibliografia') }}</textarea>
                            @error('bibliografia')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <!-- Botões -->
                        <div class="flex gap-4 mt-8">
                            <button type="submit" class="btn btn-primary flex-1">
                                ✅ Salvar Livro
                            </button>
                            <a href="{{ route('livros.index') }}" class="btn btn-ghost">
                                ❌ Cancelar
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
