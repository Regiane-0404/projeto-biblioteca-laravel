<x-mail::message>
    # Nova Avaliação Pendente

    Olá, Administrador,

    Uma nova avaliação foi submetida por um cidadão e precisa da sua atenção.

    **Detalhes da Avaliação:**
    - **Cidadão:** {{ $review->user->name }}
    - **Livro:** {{ $review->livro->nome }}
    - **Classificação:** {{ $review->classificacao }} de 5 estrelas
    - **Comentário:** "{{ $review->comentario }}"

    Por favor, aceda ao painel de moderação para aprovar ou recusar esta avaliação.

    <x-mail::button :url="url(route('admin.reviews.index', [], false))">
        Moderar Avaliações
    </x-mail::button>

    Obrigado,<br>
    Sistema {{ config('app.name') }}
</x-mail::message>
