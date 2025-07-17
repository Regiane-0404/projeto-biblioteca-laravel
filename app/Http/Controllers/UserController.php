<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;




class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Mostra a página de detalhes de um usuário específico, incluindo seu histórico.
     * Acessível apenas por Admins.
     */
    public function show(User $user)
    {
        // queremos os detalhes do livro associado. Ordenamos pela mais recente.
        $user->load(['requisicoes' => function ($query) {
            $query->with('livro')->orderBy('created_at', 'desc');
        }]);

        // ---- A LÓGICA DE DESENCRIPTAR OS NOMES DOS LIVROS ----
        // Para evitar problemas na view, preparamos os dados aqui.
        $user->requisicoes->each(function ($requisicao) {
            if ($requisicao->livro) {
                // Criamos uma propriedade temporária 'nome_visivel' com o valor desencriptado.
                $requisicao->livro->nome_visivel = $requisicao->livro->nome;
            }
        });

        // Enviamos o usuário (com seu histórico já carregado e processado) para a view.
        return view('users.show', compact('user'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,cidadao',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'Usuário criado com sucesso!');
    }

    public function edit(User $user)
    {
        // Carregamos as duas relações de requisições que a view precisa.
        $user->load('requisicoes', 'requisicoesAtivas');

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:admin,cidadao',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        // Só atualizar senha se fornecida
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6|confirmed']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        // Método removido - usar toggleStatus() para inativar
        return back()->with('error', 'Use o botão Ativar/Inativar para gerenciar usuários.');
    }

    public function toggleStatus(User $user)
    {
        // Regra de segurança para não se auto-inativar.
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Você não pode alterar o status da sua própria conta.');
        }

        // 1. Inverte o valor do atributo 'ativo'.
        // Se for 1 (true), torna-se 0 (false). Se for 0, torna-se 1.
        $user->ativo = !$user->ativo;

        // 2. Salva explicitamente o modelo inteiro na base de dados.
        $user->save();

        // 3. Força o recarregamento do modelo da base de dados para garantir que temos o valor mais recente.
        $user->refresh();

        // 4. Cria a mensagem de sucesso com o valor GARANTIDAMENTE atualizado.
        $mensagemStatus = $user->ativo ? 'ativado' : 'inativado';

        // 5. Retorna com a mensagem correta.
        return back()->with('success', "Usuário {$mensagemStatus} com sucesso!");
    }
}
