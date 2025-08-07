<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMoradaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Permite que qualquer utilizador autenticado faça o pedido
    }

    public function rules(): array
    {
        return [
            'nome_completo' => 'required|string|max:255',
            'morada'        => 'required|string|max:255',
            'complemento'   => 'nullable|string|max:255',
            'codigo_postal' => ['required', 'string', 'regex:/^\d{4}-\d{3}$/'],
            'localidade'    => 'required|string|max:255',
            'pais'          => 'required|string|max:255',
            'nif'           => 'nullable|string|digits:9',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_postal.regex' => 'O formato do código postal deve ser XXXX-XXX.',
            'nif.digits' => 'O NIF deve conter exatamente 9 dígitos.',
        ];
    }
}
