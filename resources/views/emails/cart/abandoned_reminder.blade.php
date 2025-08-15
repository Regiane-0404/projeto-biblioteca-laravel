<!DOCTYPE html>
<html>

<head>
    <title>Esqueceu-se de algo no seu carrinho?</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <div style="max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h2>Olá, {{ $userName }}!</h2>
        <p>Notámos que deixou alguns itens no seu carrinho de compras. Será que precisa de ajuda para finalizar a sua
            encomenda?</p>

        @if ($cartItems->isNotEmpty())
            <p><strong>Itens no seu carrinho:</strong></p>
            <ul style="list-style-type: none; padding: 0;">
                @foreach ($cartItems as $item)
                    <li style="margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                        <strong>{{ $item->livro->nome }}</strong> (Quantidade: {{ $item->quantity }})
                    </li>
                @endforeach
            </ul>
        @endif

        <p>Se estiver pronto para finalizar a sua compra, pode voltar ao seu carrinho a qualquer momento.</p>

        <p style="text-align: center; margin-top: 30px;">
            <a href="{{ route('cart.index') }}"
                style="background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
                Voltar ao Carrinho
            </a>
        </p>

        <p style="font-size: 0.9em; color: #777; margin-top: 20px;">
            Se já finalizou a sua encomenda, pode ignorar este email. Se tiver alguma questão, não hesite em
            contactar-nos.
        </p>

        <p>Com os melhores cumprimentos,<br>
            A Equipa da {{ config('app.name') }}</p>
    </div>
</body>

</html>
