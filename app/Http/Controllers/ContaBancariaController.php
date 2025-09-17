<?php

namespace App\Http\Controllers;

use App\Models\ContaBancaria;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContaBancariaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ContaBancaria::with('user');
        
        // Filtro por tipo de conta
        if ($request->has('tipo')) {
            $tipo = $request->get('tipo');
            $query->where('tipo_conta', $tipo);
        }
        
        $contasBancarias = $query->get();
        return view('contas-bancarias.index', compact('contasBancarias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('status_aprovacao', 'aprovado')->get();
        return view('contas-bancarias.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'agencia' => 'required|string|max:10',
            'tipo_conta' => 'required|in:corrente,poupanca,salario',
            'limite' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $contaBancaria = ContaBancaria::create([
            'user_id' => $request->user_id,
            'numero_conta' => ContaBancaria::gerarNumeroConta(),
            'agencia' => $request->agencia,
            'tipo_conta' => $request->tipo_conta,
            'limite' => $request->limite ?? 0,
            'saldo' => 0,
            'status' => 'ativa',
        ]);

        return redirect()->route('contas-bancarias.show', $contaBancaria->id)
            ->with('success', 'Conta bancária criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $contaBancaria = ContaBancaria::with('user')->findOrFail($id);
        return view('contas-bancarias.show', compact('contaBancaria'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $contaBancaria = ContaBancaria::findOrFail($id);
        $users = User::where('status_aprovacao', 'aprovado')->get();
        return view('contas-bancarias.edit', compact('contaBancaria', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $contaBancaria = ContaBancaria::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'agencia' => 'required|string|max:10',
            'tipo_conta' => 'required|in:corrente,poupanca,salario',
            'limite' => 'nullable|numeric|min:0',
            'status' => 'required|in:ativa,inativa,bloqueada',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $contaBancaria->update([
            'agencia' => $request->agencia,
            'tipo_conta' => $request->tipo_conta,
            'limite' => $request->limite ?? 0,
            'status' => $request->status,
        ]);

        return redirect()->route('contas-bancarias.show', $contaBancaria->id)
            ->with('success', 'Conta bancária atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $contaBancaria = ContaBancaria::findOrFail($id);
        $contaBancaria->delete();

        return redirect()->route('contas-bancarias.index')
            ->with('success', 'Conta bancária removida com sucesso!');
    }
}