<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PessoaJuridica;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PessoaJuridicaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PessoaJuridica::with('user');
        
        // Filtro por tipo de conta
        if ($request->has('tipo_conta')) {
            $tipoConta = $request->get('tipo_conta');
            $query->whereHas('user.contasBancarias', function($q) use ($tipoConta) {
                $q->where('tipo_conta', $tipoConta);
            });
        }
        
        $pessoasJuridicas = $query->get();
        
        // Adicionar informações de contas bancárias
        $pessoasJuridicas->each(function($pessoa) {
            $pessoa->contas_info = $pessoa->user->contasBancarias->groupBy('tipo_conta')->map(function($contas) {
                return $contas->count();
            });
        });
        
        return view('pessoa-juridica.index', compact('pessoasJuridicas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('tipo_usuario', 'pessoa_juridica')->get();
        return view('pessoa-juridica.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'cnpj' => 'required|string|size:14|unique:pessoa_juridica,cnpj',
            'inscricao_estadual' => 'nullable|string|max:20',
            'inscricao_municipal' => 'nullable|string|max:20',
            'telefone' => 'nullable|string|max:15',
            'celular' => 'nullable|string|max:15',
            'endereco' => 'required|string|max:255',
            'numero' => 'required|string|max:10',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|size:2',
            'cep' => 'required|string|size:8',
            'representante_legal' => 'required|string|max:255',
            'cpf_representante' => 'required|string|size:11',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $pessoaJuridica = PessoaJuridica::create($request->all());

        return redirect()->route('pessoa-juridica.show', $pessoaJuridica->id)
            ->with('success', 'Pessoa jurídica cadastrada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pessoaJuridica = PessoaJuridica::with('user')->findOrFail($id);
        return view('pessoa-juridica.show', compact('pessoaJuridica'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pessoaJuridica = PessoaJuridica::with('user')->findOrFail($id);
        return view('pessoa-juridica.edit', compact('pessoaJuridica'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pessoaJuridica = PessoaJuridica::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'cnpj' => 'required|string|size:14|unique:pessoa_juridica,cnpj,' . $id,
            'inscricao_estadual' => 'nullable|string|max:20',
            'inscricao_municipal' => 'nullable|string|max:20',
            'telefone' => 'nullable|string|max:15',
            'celular' => 'nullable|string|max:15',
            'endereco' => 'required|string|max:255',
            'numero' => 'required|string|max:10',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|size:2',
            'cep' => 'required|string|size:8',
            'representante_legal' => 'required|string|max:255',
            'cpf_representante' => 'required|string|size:11',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $pessoaJuridica->update($request->all());

        return redirect()->route('pessoa-juridica.show', $pessoaJuridica->id)
            ->with('success', 'Pessoa jurídica atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pessoaJuridica = PessoaJuridica::findOrFail($id);
        $pessoaJuridica->delete();

        return redirect()->route('pessoa-juridica.index')
            ->with('success', 'Pessoa jurídica removida com sucesso!');
    }
}
