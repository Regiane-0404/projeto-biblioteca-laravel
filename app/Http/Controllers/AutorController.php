<?php

namespace App\Http\Controllers;

use App\Models\Autor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AutorController extends Controller
{
    public function index(Request $request)
    {


        // Query básica
        $query = Autor::query();
                    
                    // Pesquisa por nome
            if ($request->filled('search')) {
                $search = trim($request->search);
                
                // Busca descriptografando cada registro:
                $todosAutores = Autor::all();
                $autoresEncontrados = $todosAutores->filter(function($autor) use ($search) {
                    return stripos($autor->nome, $search) !== false;
                })->pluck('id');
                
                if ($autoresEncontrados->count() > 0) {
                    $query->whereIn('id', $autoresEncontrados);
                } else {
                    $query->where('id', 0); // Nenhum resultado
                }
            }
        
        // Ordenação
        $orderBy = $request->get('order_by', 'created_at');
        $orderDirection = $request->get('order_direction', 'desc');
        
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }
        
        $query->orderBy($orderBy, $orderDirection);
        
        // Paginação
        $autores = $query->paginate(12)->appends($request->all());
        
        

        return view('autores.index', compact('autores'));
    }

    public function create()
    {
        return view('autores.create');
    }

    public function store(Request $request)
    {
        // Validação
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'nome.required' => 'O nome do autor é obrigatório',
            'foto.image' => 'O arquivo deve ser uma imagem',
            'foto.mimes' => 'Formatos aceitos: JPEG, PNG, JPG, GIF',
            'foto.max' => 'A imagem não pode ser maior que 2MB'
        ]);

        // Upload da foto
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('autores', 'public');
        }

        // Criar autor
        Autor::create([
            'nome' => $validated['nome'],
            'foto' => $fotoPath,
        ]);

        return redirect()->route('autores.index')
                        ->with('success', 'Autor criado com sucesso!');
    }

    public function show(Autor $autor)
    {
        return view('autores.show', compact('autor'));
    }

    public function edit(Autor $autor)
    {
        return view('autores.edit', compact('autor'));
    }

    public function update(Request $request, Autor $autor)
    {
        // Validação
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'nome.required' => 'O nome do autor é obrigatório',
            'foto.image' => 'O arquivo deve ser uma imagem',
            'foto.mimes' => 'Formatos aceitos: JPEG, PNG, JPG, GIF',
            'foto.max' => 'A imagem não pode ser maior que 2MB'
        ]);

        // Upload de nova foto
        $fotoPath = $autor->foto;
        if ($request->hasFile('foto')) {
            // Deletar foto antiga
            if ($autor->foto && Storage::disk('public')->exists($autor->foto)) {
                Storage::disk('public')->delete($autor->foto);
            }
            
            $fotoPath = $request->file('foto')->store('autores', 'public');
        }

        // Atualizar autor
        $autor->update([
            'nome' => $validated['nome'],
            'foto' => $fotoPath,
        ]);

        return redirect()->route('autores.index')
                        ->with('success', 'Autor atualizado com sucesso!');
    }

    public function destroy(Autor $autor)
    {
        // Verificar se tem livros associados
        if ($autor->livros()->count() > 0) {
            return redirect()->route('autores.index')
                           ->with('error', 'Não é possível excluir este autor. Ele está associado a livros.');
        }

        // Deletar foto se existir
        if ($autor->foto && Storage::disk('public')->exists($autor->foto)) {
            Storage::disk('public')->delete($autor->foto);
        }

        // Deletar autor
        $nome = $autor->nome;
        $autor->delete();

        return redirect()->route('autores.index')
                        ->with('success', "Autor \"{$nome}\" foi excluído com sucesso!");
    }

    
}