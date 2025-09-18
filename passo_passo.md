# 🏦 Grigolli Bank - Guia de Testes Passo a Passo

## 📋 Pré-requisitos

### 1. Configuração do Banco de Dados
```bash
# 1. Configurar arquivo .env com dados do banco
# 2. Executar migrations
php artisan migrate

# 3. Executar seeders (dados de teste)
php artisan db:seed

# 4. Iniciar servidor
php artisan serve
```

### 2. Verificações
- Sistema rodando em `http://localhost:8000`
- Usuários de teste criados (admin e clientes)
- Banco de dados configurado

### 3. Comandos de Configuração Completa
```bash
# Instalar dependências
composer install

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Configurar banco de dados no .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=lexxen
# DB_USERNAME=root
# DB_PASSWORD=

# Executar migrations
php artisan migrate

# Executar seeders
php artisan db:seed

# Iniciar servidor
php artisan serve
```

### 4. Verificar Instalação
```bash
# Verificar se migrations foram executadas
php artisan migrate:status

# Verificar se seeders foram executados
php artisan tinker
>>> User::count()
>>> ContaBancaria::count()
>>> Carteira::count()
```

---

## 🔐 1. Acesso ao Sistema

### 1.1 Login como Administrador
```
URL: http://localhost:8000
Email: grigolli@bank.com
Senha: 12345678
```

### 1.2 Login como Cliente
```
URL: http://localhost:8000
Email: maria.silva@example.com
Senha: password
```

---

## 👨‍💼 2. Funcionalidades do Administrador

### 2.1 Aprovar Usuários
1. **Acesse**: Dashboard do Admin
2. **Clique**: "Usuários" no menu
3. **Filtre**: Status "Aguardando Aprovação"
4. **Aprove**: Usuários pendentes
5. **Verifique**: Status mudou para "Aprovado"

### 2.2 Gerenciar Contas Bancárias
1. **Acesse**: "Pessoa Física" ou "Pessoa Jurídica"
2. **Visualize**: Lista de usuários
3. **Clique**: Em um usuário específico
4. **Aprove/Bloqueie**: Contas bancárias
5. **Verifique**: Status atualizado

### 2.3 Visualizar Estatísticas
1. **Dashboard**: Mostra totais gerais
2. **Gráficos**: Usuários ativos, contas, etc.
3. **Filtros**: Por tipo de usuário e status

---

## 👤 3. Funcionalidades do Cliente

### 3.1 Visualizar Minha Carteira
1. **Login**: Como cliente
2. **Dashboard**: Visualiza contas e saldos
3. **Verifique**: 
   - Contas ATIVAS mostram saldo real
   - Contas BLOQUEADAS mostram R$ 0,00
   - Contas AGUARDANDO mostram R$ 0,00

### 3.2 Abrir Nova Carteira
1. **Clique**: "Abrir Nova Carteira"
2. **Selecione**: Tipo (Corrente PF, Poupança PF, etc.)
3. **Confirme**: Abertura da carteira
4. **Verifique**: Nova carteira aparece como "Aguardando Aprovação"
5. **Saldo**: Deve mostrar R$ 0,00

### 3.3 Realizar Transferências

#### 3.3.1 Entre Minhas Carteiras
1. **Clique**: "Transferir" em uma conta ativa
2. **Selecione**: "Entre Minhas Carteiras"
3. **Escolha**: Carteira de destino
4. **Digite**: Valor e descrição
5. **Confirme**: Transferência
6. **Verifique**: Saldos atualizados

#### 3.3.2 Para Outros Usuários
1. **Clique**: "Transferir" em uma conta ativa
2. **Selecione**: "Para Outros"
3. **Digite**: Agência e conta de destino
4. **Aguarde**: Nome do beneficiário aparecer
5. **Digite**: Valor e descrição
6. **Confirme**: Transferência
7. **Verifique**: Transferência processada

### 3.4 Consultar Extrato

#### 3.4.1 Extrato Geral
1. **Menu**: "Extrato" → "Extrato Geral"
2. **Filtros**: Por carteira, tipo, período
3. **Visualize**: Todas as transações
4. **Verifique**: Contas de origem e destino mostradas

#### 3.4.2 Extrato por Carteira
1. **Clique**: "Extrato" em uma carteira específica
2. **Filtros**: Por tipo e período
3. **Visualize**: Transações da carteira
4. **Verifique**: Informações detalhadas

---

## 🧪 4. Cenários de Teste

### 4.1 Teste de Saldos
**Objetivo**: Verificar se saldos são exibidos corretamente

1. **Conta ATIVA**: Deve mostrar saldo real
2. **Conta BLOQUEADA**: Deve mostrar R$ 0,00
3. **Conta AGUARDANDO**: Deve mostrar R$ 0,00
4. **Nova carteira**: Deve começar com R$ 0,00

### 4.2 Teste de Transferências
**Objetivo**: Verificar se transferências funcionam corretamente

1. **Entre carteiras**: Saldo deve ser transferido
2. **Para outros**: Deve processar imediatamente
3. **Validações**: Saldo insuficiente, conta inexistente
4. **Extrato**: Deve registrar transferências

### 4.3 Teste de Aprovação
**Objetivo**: Verificar fluxo de aprovação

1. **Cliente**: Abre nova carteira
2. **Admin**: Aprova a carteira
3. **Cliente**: Verifica que carteira está ativa
4. **Saldo**: Deve continuar R$ 0,00

### 4.4 Teste de Busca de Beneficiário
**Objetivo**: Verificar busca em tempo real

1. **Digite**: Agência (ex: 1981)
2. **Digite**: Conta (ex: 75604-08)
3. **Aguarde**: 500ms após parar de digitar
4. **Verifique**: Nome do beneficiário aparece
5. **Teste**: Conta inexistente (não deve aparecer)

---

## 🔍 5. Verificações Importantes

### 5.1 Saldos Corretos
- ✅ Contas ativas: Saldo real
- ✅ Contas bloqueadas: R$ 0,00
- ✅ Contas aguardando: R$ 0,00
- ✅ Novas carteiras: R$ 0,00

### 5.2 Transferências
- ✅ Processamento imediato
- ✅ Validações funcionando
- ✅ Extrato atualizado
- ✅ Saldos sincronizados

### 5.3 Interface
- ✅ Responsiva (mobile/desktop)
- ✅ Mensagens de erro claras
- ✅ Confirmações de sucesso
- ✅ Loading states

### 5.4 Segurança
- ✅ Apenas donos veem suas contas
- ✅ Validações no backend
- ✅ Transações atômicas

---

## 🐛 6. Problemas Conhecidos e Soluções

### 6.1 Problemas de Migração
**Problema**: Erro ao executar `php artisan migrate`
**Solução**: 
```bash
# Verificar status das migrations
php artisan migrate:status

# Executar migrations pendentes
php artisan migrate

# Se houver erro, verificar logs
tail -f storage/logs/laravel.log
```

### 6.2 Problemas de Banco de Dados
**Problema**: "Connection refused" ou "Access denied"
**Solução**: 
```bash
# Verificar configuração no .env
# Testar conexão
php artisan tinker
>>> DB::connection()->getPdo();
```

### 6.3 Problemas de Seeder
**Problema**: Dados de teste não foram criados
**Solução**:
```bash
# Executar seeders específicos
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=ContaBancariaSeeder
php artisan db:seed --class=CarteiraSeeder
```

### 6.4 Saldo Incorreto
**Problema**: Conta mostra saldo de outra carteira
**Solução**: Verificar se conta tem carteira específica (com agência/conta no nome)

### 6.5 Transferência Falha
**Problema**: "Conta de destino não encontrada"
**Solução**: Verificar se conta está ativa e agência/conta estão corretos

### 6.6 Beneficiário Não Aparece
**Problema**: Nome não aparece ao digitar conta
**Solução**: Verificar se conta existe e está ativa

---

## 📞 7. Contatos de Suporte

- **Desenvolvedor**: Sistema Grigolli Bank
- **Versão**: 1.0
- **Última atualização**: 18/09/2025

---

## 🎯 8. Checklist de Testes

### Funcionalidades Básicas
- [ ] Login admin
- [ ] Login cliente
- [ ] Visualizar dashboard
- [ ] Aprovar usuários
- [ ] Gerenciar contas

### Transferências
- [ ] Entre carteiras
- [ ] Para outros usuários
- [ ] Validações de saldo
- [ ] Busca de beneficiário

### Extratos
- [ ] Extrato geral
- [ ] Extrato por carteira
- [ ] Filtros funcionando
- [ ] Contas origem/destino

### Saldos
- [ ] Contas ativas (saldo real)
- [ ] Contas bloqueadas (R$ 0,00)
- [ ] Contas aguardando (R$ 0,00)
- [ ] Novas carteiras (R$ 0,00)

---

**✅ Sistema pronto para uso em produção!**
