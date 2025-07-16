<x-mail::message>
    # Lembrete de Devolução de Livro

    Olá, **{{ $requisicao->user->name }}**,

    Este é um lembrete amigável de que a sua requisição para o livro abaixo deve ser devolvida **amanhã**, dia
    **{{ $requisicao->data_fim_prevista->format('d/m/Y') }}**.

    ---

    ### Detalhes da Requisição

    **Número:** {{ $requisicao->numero_sequencial }} <br>
    **Data do Pedido:** {{ $requisicao->data_inicio->format('d/m/Y') }}

    <x-mail::panel>
        **Livro:** {{ $requisicao->livro->nome }} <br>
        **Autor(es):** {{ $requisicao->livro->autores->pluck('nome')->join(', ') }}
    </x-mail::panel>

    Por favor, não se esqueça de devolver o livro a tempo para evitar possíveis atrasos e para que outros cidadãos
    também possam aproveitar a leitura.

    <x-mail::button :url="route('requisicoes.index')">
        Ver Minhas Requisições
    </x-mail::button>

    Obrigado,<br>
    {{ config('app.name') }}
</x-mail::message>
