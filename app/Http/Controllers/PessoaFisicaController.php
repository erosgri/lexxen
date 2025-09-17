<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PessoaFisica;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PessoaFisicaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PessoaFisica::with('user');
        
        // Filtro por tipo de conta
        if ($request->has('tipo_conta')) {
            $tipoConta = $request->get('tipo_conta');
            $query->whereHas('user.contasBancarias', function($q) use ($tipoConta) {
                $q->where('tipo_conta', $tipoConta);
            });
        }
        
        $pessoasFisicas = $query->get();
        
        // Adicionar informações de contas bancárias
        $pessoasFisicas->each(function($pessoa) {
            $pessoa->contas_info = $pessoa->user->contasBancarias->groupBy('tipo_conta')->map(function($contas) {
                return $contas->count();
            });
        });
        
        return view('pessoa-fisica.index', compact('pessoasFisicas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('tipo_usuario', 'pessoa_fisica')->get();
        return view('pessoa-fisica.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'nome_completo' => 'required|string|max:255',
            'cpf' => 'required|string|size:11|unique:pessoa_fisica,cpf',
            'rg' => 'nullable|string|max:20',
            'data_nascimento' => 'required|date',
            'sexo' => 'nullable|in:M,F,O',
            'telefone' => 'nullable|string|max:15',
            'celular' => 'nullable|string|max:15',
            'endereco' => 'required|string|max:255',
            'numero' => 'required|string|max:10',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|size:2',
            'cep' => 'required|string|size:8',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $pessoaFisica = PessoaFisica::create($request->all());

        return redirect()->route('pessoa-fisica.show', $pessoaFisica->id)
            ->with('success', 'Pessoa física cadastrada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pessoaFisica = PessoaFisica::with('user')->findOrFail($id);
        return view('pessoa-fisica.show', compact('pessoaFisica'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pessoaFisica = PessoaFisica::with('user')->findOrFail($id);
        return view('pessoa-fisica.edit', compact('pessoaFisica'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pessoaFisica = PessoaFisica::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nome_completo' => 'required|string|max:255',
            'cpf' => 'required|string|size:11|unique:pessoa_fisica,cpf,' . $id,
            'rg' => 'nullable|string|max:20',
            'data_nascimento' => 'required|date',
            'sexo' => 'nullable|in:M,F,O',
            'telefone' => 'nullable|string|max:15',
            'celular' => 'nullable|string|max:15',
            'endereco' => 'required|string|max:255',
            'numero' => 'required|string|max:10',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|size:2',
            'cep' => 'required|string|size:8',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $pessoaFisica->update($request->all());

        return redirect()->route('pessoa-fisica.show', $pessoaFisica->id)
            ->with('success', 'Pessoa física atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pessoaFisica = PessoaFisica::findOrFail($id);
        $pessoaFisica->delete();

        return redirect()->route('pessoa-fisica.index')
            ->with('success', 'Pessoa física removida com sucesso!');
    }
}
