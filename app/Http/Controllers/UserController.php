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
        if ($request->has('status') && !empty($request->get('status'))) {
            $status = $request->get('status');
            $query->where('status_aprovacao', $status);
        }
        
        // Filtro por tipo de usuário
        if ($request->has('tipo') && !empty($request->get('tipo'))) {
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
    public function reprove(Request $request, User $user)
    {
        $request->validate(['motivo_reprovacao' => 'required|string|min:10']);
        $user->update([
            'status_aprovacao' => 'reprovado',
            'motivo_reprovacao' => $request->motivo_reprovacao,
            'aprovado_em' => null,
        ]);
        return back()->with('success', 'Usuário reprovado com sucesso.');
    }

    /**
     * Bloquear um usuário.
     */
    public function block(User $user)
    {
        $user->update(['status_aprovacao' => 'bloqueado']);
        return back()->with('success', 'Usuário bloqueado com sucesso.');
    }

    /**
     * Desbloquear um usuário.
     */
    public function unblock(User $user)
    {
        $user->update(['status_aprovacao' => 'aprovado']);
        return back()->with('success', 'Usuário desbloqueado com sucesso.');
    }

    /**
     * Aprovação em lote de usuários.
     */
    public function batchApprove(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,block',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'reason' => 'required_if:action,reject,block|string|min:10'
        ]);

        $userIds = $request->user_ids;
        $action = $request->action;
        $reason = $request->reason;

        $users = User::whereIn('id', $userIds)->get();
        $successCount = 0;
        $errors = [];

        foreach ($users as $user) {
            try {
                if ($action === 'approve') {
                    $user->update([
                        'status_aprovacao' => 'aprovado',
                        'aprovado_em' => now(),
                        'motivo_reprovacao' => null,
                    ]);
                } elseif ($action === 'reject') {
                    $user->update([
                        'status_aprovacao' => 'reprovado',
                        'motivo_reprovacao' => $reason,
                        'aprovado_em' => null,
                    ]);
                } elseif ($action === 'block') {
                    $user->update([
                        'status_aprovacao' => 'bloqueado',
                        'motivo_reprovacao' => $reason,
                        'aprovado_em' => null,
                    ]);
                }
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Erro ao processar usuário {$user->email}: " . $e->getMessage();
            }
        }

        $message = "Ação executada com sucesso! {$successCount} usuário(s) processado(s).";
        if (!empty($errors)) {
            $message .= " Erros: " . implode(', ', $errors);
        }

        return back()->with('success', $message);
    }
}
