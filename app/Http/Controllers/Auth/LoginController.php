<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Mostra o formulário de login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Lida com a tentativa de login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Busca o usuário pelo email
        $user = User::where('email', $credentials['email'])->first();

        // **Verificação customizada antes de tentar autenticar**
        if ($user) {
            // **Verifica se o usuário está reprovado**
            if ($user->isReprovado()) {
                return back()->withErrors([
                    'email' => 'Sua conta foi reprovada. Entre em contato com o suporte.',
                ])->onlyInput('email');
            }

            // **Verifica se o usuário está aguardando aprovação**
            if ($user->isAguardandoAprovacao()) {
                return back()->withErrors([
                    'email' => 'Sua conta está aguardando aprovação.',
                ])->onlyInput('email');
            }
        }

        // Tenta autenticar o usuário
        if (Auth::attempt($credentials)) {
            // Regenera a sessão para evitar session fixation
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ])->onlyInput('email');
    }

    /**
     * Lida com o logout do usuário.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
