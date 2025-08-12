<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatura #{{ $encomenda->numero_encomenda }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        .header,
        .footer {
            text-align: center;
        }

        .header h1 {
            margin: 0;
        }

        .details,
        .items,
        .totals {
            width: 100%;
            margin-top: 20px;
        }

        .details table,
        .items table,
        .totals table {
            width: 100%;
            border-collapse: collapse;
        }

        .details table td {
            vertical-align: top;
            padding: 5px;
        }

        .items th,
        .items td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        .items th {
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        .totals {
            width: 50%;
            margin-left: auto;
        }

        .totals td {
            padding: 5px;
        }

        .totals .strong {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name', 'A Sua Loja') }}</h1>
            <p>Fatura Simplificada</p>
        </div>

        <div class="details">
            <table>
                <tr>
                    <td>
                        <strong>Fatura Nº:</strong> {{ $encomenda->numero_encomenda }}<br>
                        <strong>Data da Fatura:</strong> {{ now()->format('d/m/Y') }}<br>
                        <strong>Data da Encomenda:</strong> {{ $encomenda->created_at->format('d/m/Y') }}
                    </td>
                    <td class="text-right">
                        <strong>Faturado a:</strong><br>
                        {{ $encomenda->moradaFaturacao->nome_completo }}<br>
                        {{ $encomenda->moradaFaturacao->morada }}<br>
                        @if ($encomenda->moradaFaturacao->complemento)
                            {{ $encomenda->moradaFaturacao->complemento }}<br>
                        @endif
                        {{ $encomenda->moradaFaturacao->codigo_postal }}
                        {{ $encomenda->moradaFaturacao->localidade }}<br>
                        @if ($encomenda->moradaFaturacao->nif)
                            <strong>NIF:</strong> {{ $encomenda->moradaFaturacao->nif }}
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <div class="items">
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qtd.</th>
                        <th>Preço Unit.</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($encomenda->itens as $item)
                        <tr>
                            <td>{{ $item->livro->nome }}</td>
                            <td>{{ $item->quantidade }}</td>
                            <td class="text-right">€ {{ number_format($item->preco, 2, ',', '.') }}</td>
                            <td class="text-right">€ {{ number_format($item->preco * $item->quantidade, 2, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">€ {{ number_format($encomenda->subtotal, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Portes de Envio:</td>
                    <td class="text-right">€ {{ number_format($encomenda->portes_envio, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Impostos (IVA):</td>
                    <td class="text-right">€ {{ number_format($encomenda->impostos, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="strong">Total a Pagar:</td>
                    <td class="text-right strong">€ {{ number_format($encomenda->total, 2, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>Obrigado pela sua preferência!</p>
        </div>
    </div>
</body>

</html>
