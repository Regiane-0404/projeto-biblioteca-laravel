<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-base-content">
            🚚 Morada de Entrega
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <form action="{{ route('checkout.morada.store') }}" method="POST" class="space-y-6">
                        @csrf
                        @if ($errors->any())
                            <div style="background: #f8d7da; color: #721c24; padding: 15px;">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li><strong>{{ $error }}</strong></li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <h3 class="text-lg font-bold mb-4 text-left">Informações de Envio</h3>
                        <div class="space-y-4">
                            <!-- Nome Completo -->
                            <div class="form-control w-full">
                                <label class="label"><span class="label-text font-medium">Nome Completo</span></label>
                                <input type="text" name="nome_completo"
                                    value="{{ old('nome_completo', $ultimaMorada->nome_completo ?? $user->name) }}"
                                    class="input input-bordered w-full" required>
                            </div>

                            <!-- Código Postal -->
                            <div class="form-control w-full">
                                <label class="label"><span class="label-text font-medium">Código Postal (Formato:
                                        XXXX-XXX)</span></label>
                                <input type="text" id="codigo_postal" name="codigo_postal"
                                    value="{{ old('codigo_postal', $ultimaMorada->codigo_postal ?? '') }}"
                                    class="input input-bordered w-full" required>
                            </div>

                            <!-- Morada -->
                            <div class="form-control w-full">
                                <label class="label"><span class="label-text font-medium">Morada</span></label>
                                <input type="text" id="morada" name="morada"
                                    value="{{ old('morada', $ultimaMorada->morada ?? '') }}"
                                    class="input input-bordered w-full" required>
                            </div>

                            <!-- Complemento -->
                            <div class="form-control w-full">
                                <label class="label"><span class="label-text font-medium">Complemento (Andar, Porta,
                                        etc.)</span></label>
                                <input type="text" name="complemento"
                                    value="{{ old('complemento', $ultimaMorada->complemento ?? '') }}"
                                    class="input input-bordered w-full">
                            </div>

                            <!-- Localidade -->
                            <div class="form-control w-full">
                                <label class="label"><span class="label-text font-medium">Localidade</span></label>
                                <input type="text" id="localidade" name="localidade"
                                    value="{{ old('localidade', $ultimaMorada->localidade ?? '') }}"
                                    class="input input-bordered w-full" required>
                            </div>

                            <!-- País (Tom Select) -->
                            <div class="form-control w-full">
                                <label class="label"><span class="label-text font-medium">País</span></label>
                                <select id="country-select" name="pais" class="tom-select w-full">
                                    <option value="">Selecione um país</option>
                                    <!-- JS preencherá as opções -->
                                </select>
                            </div>

                            <!-- NIF -->
                            <div class="form-control w-full">
                                <label class="label"><span class="label-text font-medium">NIF (Contribuinte -
                                        opcional)</span></label>
                                <input type="text" name="nif" value="{{ old('nif', $ultimaMorada->nif ?? '') }}"
                                    class="input input-bordered w-full">
                            </div>
                        </div>

                        <div class="card-actions justify-end mt-6">
                            <a href="{{ route('cart.index') }}" class="btn btn-ghost">Voltar ao Carrinho</a>
                            <button type="submit" class="btn btn-primary">Continuar para Pagamento</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.0/dist/css/tom-select.css" rel="stylesheet" />
    <!-- Tom Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.0/dist/js/tom-select.complete.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const countries = [
                'Afeganistão', 'África do Sul', 'Albânia', 'Alemanha', 'Andorra', 'Angola', 'Antígua e Barbuda',
                'Arábia Saudita', 'Argélia', 'Argentina', 'Arménia', 'Austrália', 'Áustria', 'Azerbaijão',
                'Bahamas', 'Bangladexe', 'Barbados', 'Barém', 'Bélgica', 'Belize', 'Benim', 'Bielorrússia',
                'Bolívia', 'Bósnia e Herzegovina', 'Botsuana', 'Brasil', 'Brunei', 'Bulgária', 'Burquina Faso',
                'Burúndi', 'Butão', 'Cabo Verde', 'Camarões', 'Camboja', 'Canadá', 'Catar', 'Cazaquistão',
                'Chade', 'Chile', 'China', 'Chipre', 'Colômbia', 'Comores', 'Congo-Brazzaville',
                'Congo-Kinshasa', 'Coreia do Norte', 'Coreia do Sul', 'Cosovo', 'Costa do Marfim',
                'Costa Rica', 'Croácia', 'Cuba', 'Dinamarca', 'Dominica', 'Egito', 'Emirados Árabes Unidos',
                'Equador', 'Eritreia', 'Eslováquia', 'Eslovénia', 'Espanha', 'Estados Unidos', 'Estónia',
                'Essuatíni', 'Etiópia', 'Fiji', 'Filipinas', 'Finlândia', 'França', 'Gabão', 'Gâmbia',
                'Gana', 'Geórgia', 'Granada', 'Grécia', 'Guatemala', 'Guiana', 'Guiné', 'Guiné-Bissau',
                'Guiné Equatorial', 'Haiti', 'Holanda', 'Honduras', 'Hungria', 'Iémen', 'Índia',
                'Indonésia', 'Irão', 'Iraque', 'Irlanda', 'Islândia', 'Israel', 'Itália', 'Jamaica', 'Japão',
                'Jibuti', 'Jordânia', 'Laos', 'Lesoto', 'Letónia', 'Líbano', 'Libéria', 'Líbia', 'Listenstaine',
                'Lituânia', 'Luxemburgo', 'Macedónia do Norte', 'Madagáscar', 'Malásia', 'Malávi', 'Maldivas',
                'Mali', 'Malta', 'Marrocos', 'Maurícia', 'Mauritânia', 'México', 'Mianmar', 'Micronésia',
                'Moçambique', 'Moldávia', 'Mónaco', 'Mongólia', 'Montenegro', 'Namíbia', 'Nauru', 'Nepal',
                'Nicarágua', 'Níger', 'Nigéria', 'Noruega', 'Nova Zelândia', 'Omã', 'Países Baixos',
                'Palau', 'Palestina', 'Panamá', 'Papua-Nova Guiné', 'Paquistão', 'Paraguai', 'Peru',
                'Polónia', 'Portugal', 'Quénia', 'Quirguistão', 'Quiribáti', 'Reino Unido',
                'República Centro-Africana',
                'República Dominicana', 'República Tcheca', 'Roménia', 'Ruanda', 'Rússia', 'Salomão',
                'Salvador', 'Samoa', 'Santa Lúcia', 'São Cristóvão e Neves', 'São Marino',
                'São Tomé e Príncipe',
                'São Vicente e Granadinas', 'Seicheles', 'Senegal', 'Serra Leoa', 'Sérvia', 'Singapura',
                'Síria', 'Somália', 'Sri Lanca', 'Suazilândia', 'Sudão', 'Sudão do Sul', 'Suécia',
                'Suíça', 'Suriname', 'Tailândia', 'Taiwan', 'Tajiquistão', 'Tanzânia', 'Timor-Leste',
                'Togo', 'Tonga', 'Trindade e Tobago', 'Tunísia', 'Turquemenistão', 'Turquia', 'Tuvalu',
                'Ucrânia', 'Uganda', 'Uruguai', 'Usbequistão', 'Vanuatu', 'Vaticano', 'Venezuela',
                'Vietname', 'Zâmbia', 'Zimbábue'
            ];

            const oldPais = "{{ old('pais', $ultimaMorada->pais ?? 'Portugal') }}";
            const select = document.getElementById('country-select');

            countries.forEach(pais => {
                const option = document.createElement('option');
                option.value = pais;
                option.textContent = pais;
                if (pais === oldPais) {
                    option.selected = true;
                }
                select.appendChild(option);
            });

            new TomSelect('#country-select', {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: "Selecione ou digite um país..."
            });

            // Código Postal → Morada Automática
            const inputCodigoPostal = document.getElementById('codigo_postal');
            const inputMorada = document.getElementById('morada');
            const inputLocalidade = document.getElementById('localidade');

            inputCodigoPostal.addEventListener('blur', function() {
                const valorOriginal = this.value;
                const cp = valorOriginal.replace(/\D/g, '');

                if (cp.length === 7) {
                    const cpFormatado = cp.slice(0, 4) + '-' + cp.slice(4);
                    this.classList.add('loading');

                    fetch(`/api/buscar-cp/${cpFormatado}`)
                        .then(response => {
                            if (!response.ok) throw new Error('Código Postal não encontrado');
                            return response.json();
                        })
                        .then(data => {
                            inputMorada.value = data.street || '';
                            inputLocalidade.value = data.city || '';
                        })
                        .catch(error => {
                            console.error('Erro ao buscar código postal:', error);
                            inputMorada.value = '';
                            inputLocalidade.value = '';
                        })
                        .finally(() => {
                            this.classList.remove('loading');
                        });
                }
            });
        });
    </script>
</x-app-layout>
