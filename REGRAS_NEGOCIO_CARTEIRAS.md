# Regras de Negócio - Carteiras e Contas

## 📋 Regras Fundamentais

### 1. **Independência das Contas**
- ✅ Um usuário pode ter **diversas contas** em sua carteira (tanto PJ quanto PF)
- ✅ **Todas as contas são independentes** - não podem cruzar saldos
- ✅ Cada conta tem sua própria carteira "Principal" específica
- ✅ Saldos não se misturam entre contas diferentes

### 2. **Saldo Inicial das Contas**
- ✅ **Toda conta recém liberada pelo admin começa com saldo zero**
- ✅ Aplica-se tanto para contas PJ quanto PF
- ✅ Carteiras "Principal" sempre começam com R$ 0,00
- ✅ Carteiras adicionais (WALLET) sempre começam com R$ 0,00

### 3. **Estrutura de Carteiras**
- ✅ Cada conta bancária gera uma carteira "Principal - {TipoConta}"
- ✅ Usuários aprovados podem criar carteiras adicionais (WALLET)
- ✅ Carteiras são associadas ao perfil do usuário (PF ou PJ)
- ✅ Carteiras aguardam liberação até a conta bancária ser aprovada

## 🔧 Implementações Técnicas

### ContaController (abrirConta)
```php
// Sempre cria carteira com saldo zero
$owner->carteiras()->create([
    'name' => $nomeCarteira,
    'balance' => 0, // ✅ Sempre zero
    'type' => 'DEFAULT',
    'status' => 'AGUARDANDO_LIBERACAO',
    'approval_status' => 'pending',
]);
```

### CarteiraService (createCarteira)
```php
// Sempre cria carteiras com saldo zero
$carteira = $owner->carteiras()->create([
    'name' => $dto->name,
    'type' => $dto->type,
    'balance' => 0, // ✅ Sempre zero (ignora DTO)
    'status' => 'AGUARDANDO_LIBERACAO',
    'approval_status' => 'pending',
]);
```

### ContaBancariaController (approve)
```php
// Ativa apenas a carteira específica da conta aprovada
$carteira = $owner->carteiras()
    ->where('name', $nomeCarteira)
    ->where('status', 'AGUARDANDO_LIBERACAO')
    ->where('approval_status', 'pending')
    ->first();
```

### Interface (conta/index.blade.php)
```javascript
// Busca carteira específica por tipo de conta
let carteiraEspecifica = carteiras.find(carteira => {
    const tipoFormatado = conta.tipo_conta.charAt(0).toUpperCase() + conta.tipo_conta.slice(1);
    return carteira.name === `Principal - ${tipoFormatado}` || 
           carteira.name === `Principal - ${tipoFormatado} (PF)` ||
           carteira.name === `Principal - ${tipoFormatado} (PJ)`;
});
```

## ✅ Validações Implementadas

### 1. **Validação de Aprovação**
- ✅ Gate `create-carteira` verifica se usuário está aprovado
- ✅ CarteiraService valida aprovação antes de criar carteiras
- ✅ ContaController valida aprovação antes de abrir contas

### 2. **Validação de Saldo**
- ✅ Todas as carteiras novas começam com saldo zero
- ✅ Interface mostra saldo da carteira específica da conta
- ✅ Transferências validam saldo da carteira específica

### 3. **Validação de Status**
- ✅ Contas bloqueadas/aguardando não podem transferir
- ✅ Carteiras aguardando liberação não podem ser usadas
- ✅ Apenas carteiras ativas e aprovadas funcionam

## 🎯 Exemplos de Funcionamento

### Usuário PF com 3 contas:
```
Conta Corrente (ATIVA) → Principal - Corrente (PF) → R$ 0,00
Conta Poupança (ATIVA) → Principal - Poupança (PF) → R$ 0,00  
Conta Empresarial (ATIVA) → Principal - Empresarial (PF) → R$ 0,00
```

### Usuário PJ com 2 contas:
```
Conta Corrente (ATIVA) → Principal - Corrente (PJ) → R$ 0,00
Conta Empresarial (ATIVA) → Principal - Empresarial (PJ) → R$ 0,00
```

### Carteiras Adicionais (WALLET):
```
Carteira Pessoal → R$ 0,00 (independente)
Carteira Investimentos → R$ 0,00 (independente)
```

## 🚨 Pontos de Atenção

1. **Nunca misturar saldos** entre contas diferentes
2. **Sempre verificar aprovação** antes de criar carteiras
3. **Sempre começar com saldo zero** em carteiras novas
4. **Associar carteira correta** à conta na interface
5. **Validar status** antes de permitir operações

---
*Documento criado em: {{ date('Y-m-d H:i:s') }}*
*Sistema: Lexxen Banking Platform*
