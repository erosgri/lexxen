<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\PessoaFisica;
use App\Models\PessoaJuridica;
use App\Rules\ValidarCpf;
use App\Rules\ValidarCnpj;

class RegisterController extends Controller
{
    /**
     * Mostra o formulário de registro.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Lida com a submissão do formulário de registro.
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = DB::transaction(function () use ($request) {
            return $this->create($request);
        });

        Auth::login($user);

        return redirect('/');
    }
    
    /**
     * Valida os dados do formulário de registro.
     */
    protected function validator(array $data)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'tipo_usuario' => ['required', 'in:pessoa_fisica,pessoa_juridica'],
            'cep' => ['required', 'string', 'max:8'],
            'endereco' => ['required', 'string', 'max:255'],
            'numero' => ['required', 'string', 'max:10'],
            'bairro' => ['required', 'string', 'max:255'],
            'cidade' => ['required', 'string', 'max:255'],
            'estado' => ['required', 'string', 'max:2'],
        ];

        if (request()->input('tipo_usuario') === 'pessoa_fisica') {
            $rules = array_merge($rules, [
                'pf_nome_completo' => ['required', 'string', 'max:255'],
                'cpf' => ['required', 'string', 'max:14', 'unique:pessoa_fisica,cpf', new ValidarCpf],
                'rg' => ['nullable', 'string', 'max:20'],
                'data_nascimento' => ['required', 'date'],
            ]);
        }

        if (request()->input('tipo_usuario') === 'pessoa_juridica') {
            $rules = array_merge($rules, [
                'razao_social' => ['required', 'string', 'max:255'],
                'cnpj' => ['required', 'string', 'max:18', 'unique:pessoa_juridica,cnpj', new ValidarCnpj],
                'representante_legal' => ['required', 'string', 'max:255'],
                'cpf_representante' => ['required', 'string', 'max:14', new ValidarCpf],
            ]);
        }

        return Validator::make($data, $rules);
    }

    /**
     * Cria uma nova instância de usuário e o perfil associado.
     */
    protected function create(Request $request)
    {
        $data = $request->all();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'tipo_usuario' => $data['tipo_usuario'],
            'status_aprovacao' => 'aguardando', // Status inicial
        ]);

        if ($data['tipo_usuario'] === 'pessoa_fisica') {
            $user->pessoaFisica()->create([
                'nome_completo' => $data['pf_nome_completo'],
                'cpf' => $data['cpf'],
                'rg' => $data['rg'],
                'data_nascimento' => $data['data_nascimento'],
                'endereco' => $data['endereco'],
                'numero' => $data['numero'],
                'complemento' => $data['complemento'],
                'bairro' => $data['bairro'],
                'cidade' => $data['cidade'],
                'estado' => $data['estado'],
                'cep' => $data['cep'],
            ]);
        }

        if ($data['tipo_usuario'] === 'pessoa_juridica') {
            $user->pessoaJuridica()->create([
                'razao_social' => $data['razao_social'],
                'cnpj' => $data['cnpj'],
                'representante_legal' => $data['representante_legal'],
                'cpf_representante' => $data['cpf_representante'],
                'endereco' => $data['endereco'],
                'numero' => $data['numero'],
                'complemento' => $data['complemento'],
                'bairro' => $data['bairro'],
                'cidade' => $data['cidade'],
                'estado' => $data['estado'],
                'cep' => $data['cep'],
            ]);
        }

        return $user;
    }
}
