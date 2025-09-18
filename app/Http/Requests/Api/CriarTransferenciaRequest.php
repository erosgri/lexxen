<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use App\Models\Carteira;
use App\DTOs\TransferenciaDTO;
use Illuminate\Validation\Rule;

class CriarTransferenciaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $carteiraOrigem = Carteira::find($this->input('carteira_origem_id'));

        if (!$carteiraOrigem) {
            return false;
        }

        $user = $this->user();
        
        // Verificação direta de autorização (sem usar Gate)
        if ($user->tipo_usuario === 'admin') {
            return true;
        }

        if (!$carteiraOrigem->owner || !$carteiraOrigem->owner->usuario) {
            return false;
        }
        
        return $user->id === $carteiraOrigem->owner->usuario->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $tipo = $this->input('tipo');

        $regrasBase = [
            'tipo' => ['required', 'string', Rule::in(['entre_carteiras', 'para_outros'])],
            'carteira_origem_id' => ['required', 'integer', 'exists:carteiras,id'],
            'valor' => ['required', 'numeric', 'gt:0'],
            'descricao' => ['nullable', 'string', 'max:255'],
        ];

        if ($tipo === 'entre_carteiras') {
            return array_merge($regrasBase, [
                'carteira_destino_id' => ['required', 'integer', 'exists:carteiras,id', 'different:carteira_origem_id'],
            ]);
        }

        if ($tipo === 'para_outros') {
            return array_merge($regrasBase, [
                'agencia_destino' => ['required', 'string', 'max:10'],
                'conta_destino' => ['required', 'string', 'max:20'],
            ]);
        }

        return $regrasBase;
    }

    /**
     * Cria um DTO a partir dos dados validados da requisição.
     */
    public function toDTO(): TransferenciaDTO
    {
        return new TransferenciaDTO(
            tipo: $this->validated('tipo'),
            carteiraOrigemId: $this->validated('carteira_origem_id'),
            valor: (float) $this->validated('valor'),
            descricao: $this->validated('descricao') ?? '',
            carteiraDestinoId: $this->validated('carteira_destino_id') ?? null,
            agenciaDestino: $this->validated('agencia_destino') ?? null,
            contaDestino: $this->validated('conta_destino') ?? null
        );
    }
}
