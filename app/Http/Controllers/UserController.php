<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['pessoaFisica', 'pessoaJuridica'])
                     ->withCount('contasBancarias');
        
        // Filtro por status de aprovação
        if ($request->has('status')) {
            $status = $request->get('status');
            $query->where('status_aprovacao', $status);
        }
        
        // Filtro por tipo de usuário
        if ($request->has('tipo')) {
            $tipo = $request->get('tipo');
            $query->where('tipo_usuario', $tipo);
        }
        
        $users = $query->latest()->paginate(20);
        
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'tipo_usuario' => 'required|in:pessoa_fisica,pessoa_juridica',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tipo_usuario' => $request->tipo_usuario,
            'ativo' => true,
        ]);

        return redirect()->route('users.show', $user->id)
            ->with('success', 'Usuário cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with(['pessoaFisica', 'pessoaJuridica'])->findOrFail($id);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::with(['pessoaFisica', 'pessoaJuridica'])->findOrFail($id);
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8',
            'tipo_usuario' => 'required|in:pessoa_fisica,pessoa_juridica',
            'ativo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'email' => $request->email,
            'tipo_usuario' => $request->tipo_usuario,
            'ativo' => $request->ativo ?? $user->ativo,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.show', $user->id)
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuário removido com sucesso!');
    }

    /**
     * Aprova um usuário.
     */
    public function approve(User $user)
    {
        $user->update([
            'status_aprovacao' => 'aprovado',
            'aprovado_em' => now(),
            'motivo_reprovacao' => null,
        ]);

        return redirect()->route('users.index')->with('success', "Usuário {$user->email} aprovado com sucesso!");
    }

    /**
     * Reprova um usuário.
     */
    public function reprove(User $user)
    {
        $user->update(['status_aprovacao' => 'reprovado']);
        return redirect()->route('users.index')->with('success', 'Usuário reprovado com sucesso.');
    }
}
