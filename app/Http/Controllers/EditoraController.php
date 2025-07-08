<?php

namespace App\Http\Controllers;

use App\Models\Editora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EditoraController extends Controller
{
    public function index(Request $request)
    {
        // Query básica
        $query = Editora::query();
        
        // Pesquisa por nome
        if ($request->filled('search')) {
            $search = trim($request->search);
            
            // Busca descriptografando cada registro:
            $todasEditoras = Editora::all();
            $editorasEncontradas = $todasEditoras->filter(function($editora) use ($search) {
                return stripos($editora->nome, $search) !== false;
            })->pluck('id');
            
            if ($editorasEncontradas->count() > 0) {
                $query->whereIn('id', $editorasEncontradas);
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
        $editoras = $query->paginate(12)->appends($request->all());
        
        return view('editoras.index', compact('editoras'));
    }

    public function create()
    {
        return view('editoras.create');
    }

    public function store(Request $request)
    {
        // Validação
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'logotipo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ], [
            'nome.required' => 'O nome da editora é obrigatório',
            'logotipo.image' => 'O arquivo deve ser uma imagem',
            'logotipo.mimes' => 'Formatos aceitos: JPEG, PNG, JPG, GIF, SVG',
            'logotipo.max' => 'A imagem não pode ser maior que 2MB'
        ]);

        // Upload do logotipo
        $logotipoPath = null;
        if ($request->hasFile('logotipo')) {
            $logotipoPath = $request->file('logotipo')->store('editoras', 'public');
        }

        // Criar editora
        Editora::create([
            'nome' => $validated['nome'],
            'logotipo' => $logotipoPath,
        ]);

        return redirect()->route('editoras.index')
                        ->with('success', 'Editora criada com sucesso!');
    }

    public function show(Editora $editora)
    {
        return view('editoras.show', compact('editora'));
    }

    public function edit(Editora $editora)
    {
        return view('editoras.edit', compact('editora'));
    }

    public function update(Request $request, Editora $editora)
    {
        // Validação
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'logotipo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ], [
            'nome.required' => 'O nome da editora é obrigatório',
            'logotipo.image' => 'O arquivo deve ser uma imagem',
            'logotipo.mimes' => 'Formatos aceitos: JPEG, PNG, JPG, GIF, SVG',
            'logotipo.max' => 'A imagem não pode ser maior que 2MB'
        ]);

        // Upload de novo logotipo
        $logotipoPath = $editora->logotipo;
        if ($request->hasFile('logotipo')) {
            // Deletar logotipo antigo
            if ($editora->logotipo && Storage::disk('public')->exists($editora->logotipo)) {
                Storage::disk('public')->delete($editora->logotipo);
            }
            
            $logotipoPath = $request->file('logotipo')->store('editoras', 'public');
        }

        // Atualizar editora
        $editora->update([
            'nome' => $validated['nome'],
            'logotipo' => $logotipoPath,
        ]);

        return redirect()->route('editoras.index')
                        ->with('success', 'Editora atualizada com sucesso!');
    }

    public function destroy(Editora $editora)
    {
        // Verificar se tem livros associados
        if ($editora->livros()->count() > 0) {
            return redirect()->route('editoras.index')
                           ->with('error', 'Não é possível excluir esta editora. Ela está associada a livros.');
        }

        // Deletar logotipo se existir
        if ($editora->logotipo && Storage::disk('public')->exists($editora->logotipo)) {
            Storage::disk('public')->delete($editora->logotipo);
        }

        // Deletar editora
        $nome = $editora->nome;
        $editora->delete();

        return redirect()->route('editoras.index')
                        ->with('success', "Editora \"{$nome}\" foi excluída com sucesso!");
    }
}