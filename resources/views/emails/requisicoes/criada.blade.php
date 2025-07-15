<x-mail::message>
# Confirmação de Requisição

Olá, **{{ $requisicao->user->name }}**,

Obrigado por utilizar a nossa biblioteca!

A sua requisição para o livro abaixo foi registada com sucesso e está agora pendente de aprovação por um dos nossos administradores.

---

### Detalhes da Requisição

**Número:** {{ $requisicao->numero_sequencial }} <br>
**Data do Pedido:** {{ $requisicao->data_inicio->format('d/m/Y') }} <br>
**Data Prevista para Devolução:** {{ $requisicao->data_fim_prevista->format('d/m/Y') }}

<x-mail::panel>
**Livro:** {{ $requisicao->livro->nome }} <br>
**Autor(es):** {{ $requisicao->livro->autores->pluck('nome')->join(', ') }}
</x-mail::panel>

Pode consultar o estado de todas as suas requisições acedendo ao nosso portal.

<x-mail::button :url="route('requisicoes.index')">
Ver Minhas Requisições
</x-mail::button>

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>