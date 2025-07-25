<x-mail::message>
    # Um livro da sua lista de espera está disponível!

    Olá, **{{ $user->name }}**,

    Temos boas notícias! O livro **"{{ $livro->nome }}"**, pelo qual você demonstrou interesse, acabou de ficar
    disponível para requisição na nossa biblioteca.

    Seja rápido, pois outros leitores também podem estar interessados!

    <x-mail::panel>
        **Livro:** {{ $livro->nome }} <br>
        **Autor(es):** {{ $livro->autores->pluck('nome')->join(', ') }}
    </x-mail::panel>

    Pode aceder diretamente à página do livro para o requisitar.

    <x-mail::button :url="url('/livros/' . $livro->id)">
        Ver Detalhes do Livro
    </x-mail::button>

    Boas leituras,<br>
    A Equipa da {{ config('app.name') }}
</x-mail::message>
