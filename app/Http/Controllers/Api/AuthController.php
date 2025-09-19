<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login do usuário e geração do token
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        if (!$user->isAprovado()) {
            return response()->json([
                'message' => 'Usuário não aprovado. Aguarde a aprovação do administrador.',
                'status' => $user->status_aprovacao,
            ], 403);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login realizado com sucesso',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'tipo_usuario' => $user->tipo_usuario,
                'status_aprovacao' => $user->status_aprovacao,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Logout do usuário e revogação do token
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso',
        ]);
    }

    /**
     * Informações do usuário autenticado
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $owner = null;
        if ($user->tipo_usuario === 'pessoa_fisica') {
            $owner = $user->pessoaFisica;
        } elseif ($user->tipo_usuario === 'pessoa_juridica') {
            $owner = $user->pessoaJuridica;
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'tipo_usuario' => $user->tipo_usuario,
                'status_aprovacao' => $user->status_aprovacao,
                'aprovado_em' => $user->aprovado_em,
                'owner' => $owner,
            ],
        ]);
    }

    /**
     * Revogar todos os tokens do usuário
     */
    public function revokeAllTokens(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Todos os tokens foram revogados com sucesso',
        ]);
    }
}









