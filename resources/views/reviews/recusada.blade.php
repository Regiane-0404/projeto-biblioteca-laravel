<x-mail::message>
    # Atualização sobre a sua avaliação

    Olá, **{{ $review->user->name }}**,

    Após uma análise da nossa equipa, a sua avaliação para o livro **"{{ $review->livro->nome }}"** não pôde ser
    publicada.

    **Justificação da Moderação:**
    <x-mail::panel>
        {{ $review->justificacao_recusa }}
    </x-mail::panel>

    Agradecemos a sua compreensão e incentivamo-lo a tentar submeter uma nova avaliação que siga as nossas diretrizes da
    comunidade.

    Se tiver alguma questão, por favor, contacte o nosso suporte.

    Atenciosamente,<br>
    A Equipa da {{ config('app.name') }}
</x-mail::message>
