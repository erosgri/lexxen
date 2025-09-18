# Guia Operacional - Extrato Paginado e Filtrado

Este documento contém as credenciais de acesso e os comandos necessários para preparar e validar a funcionalidade de extrato.

## 1. Credenciais de Acesso

| Perfil      | E-mail                    | Senha      |
|-------------|---------------------------|------------|
| **Admin**   | `grigolli@bank.com`       | `12345678` |
| **Cliente A** | `jean.paes@example.com`   | `password` |
| **Cliente B** | `maria.silva@example.com` | `password` |

---

## 2. Procedimento de Preparação e Teste

Execute os seguintes comandos na ordem especificada para popular o banco de dados e validar a funcionalidade.

### Passo 1: Criar Usuários de Teste

Garante que os usuários `jean.paes` e `maria.silva` existam.

```bash
php artisan app:ensure-test-users
```

### Passo 2: Gerar Dados de Transferência

Limpa dados antigos e cria 50 novas transferências de teste entre os usuários.

```bash
php artisan db:seed --class=TestTransferSeeder
```

### Passo 3: Validar a Funcionalidade do Extrato

Executa um teste automatizado que filtra o extrato do "Sr. Jean Molina Paes" para os últimos 7 dias, com paginação, e valida se as regras de negócio foram atendidas.

```bash
php artisan app:test-extrato-filter
```
