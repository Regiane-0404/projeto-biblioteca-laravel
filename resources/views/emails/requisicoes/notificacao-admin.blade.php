<x-mail::message>
    # Nova Requisição Recebida

    Olá, Administrador,

    Uma nova requisição foi criada e precisa da sua atenção.

    **Detalhes:**
    - **Número:** {{ $requisicao->numero_sequencial }}
    - **Cidadão:** {{ $requisicao->user->name }}
    - **Livro:** {{ $requisicao->livro->nome }}
    - **Data do Pedido:** {{ $requisicao->data_inicio->format('d/m/Y') }}

    Por favor, aceda ao painel de requisições para aprovar ou gerir este pedido.

    <x-mail::button :url="url('/requisicoes?tab=lista&status=solicitado')">
        Ver Pedidos Pendentes
    </x-mail::button>

    Obrigado,<br>
    Sistema {{ config('app.name') }}
</x-mail::message>
