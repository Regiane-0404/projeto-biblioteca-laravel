<x-mail::message>
    # A sua avaliação foi publicada!

    Olá, **{{ $review->user->name }}**,

    Boas notícias! A sua avaliação para o livro **"{{ $review->livro->nome }}"** foi aprovada pela nossa equipa de
    moderação e já está visível para toda a comunidade.

    Obrigado pela sua contribuição para tornar a nossa biblioteca ainda melhor.

    <x-mail::button :url="url('/livros/' . $review->livro_id)">
        Ver a sua avaliação
    </x-mail::button>

    Atenciosamente,<br>
    A Equipa da {{ config('app.name') }}
</x-mail::message>
