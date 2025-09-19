# 🏦 Grigolli Bank - Guia Completo de Instalação e Testes

## 🚀 INSTALAÇÃO EM NOVO PC - PASSO A PASSO COMPLETO

### 1. 📋 Pré-requisitos do Sistema

#### 1.1 Software Necessário
```bash
# PHP 8.1 ou superior
php --version

# Composer (gerenciador de dependências PHP)
composer --version

# MySQL 8.0 ou MariaDB 10.3+
mysql --version

# Node.js 16+ (para assets)
node --version
npm --version

# Git (para clonar repositório)
git --version
```

#### 1.2 Instalação no Windows (WAMP/XAMPP)
```bash
# 1. Baixar e instalar WAMP Server
# https://www.wampserver.com/en/

# 2. Ou baixar e instalar XAMPP
# https://www.apachefriends.org/

# 3. Instalar Composer
# https://getcomposer.org/download/

# 4. Instalar Node.js
# https://nodejs.org/
```

#### 1.3 Instalação no Linux (Ubuntu/Debian)
```bash
# Atualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar PHP e extensões
sudo apt install php8.1 php8.1-cli php8.1-mysql php8.1-xml php8.1-mbstring php8.1-curl php8.1-zip php8.1-bcmath php8.1-gd

# Instalar MySQL
sudo apt install mysql-server

# Instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Instalar Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs
```

### 2. 🗄️ Configuração do Banco de Dados

#### 2.1 Criar Banco de Dados
```sql
-- Conectar ao MySQL
mysql -u root -p

-- Criar banco de dados
CREATE DATABASE lexxen CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Criar usuário (opcional, pode usar root)
CREATE USER 'lexxen_user'@'localhost' IDENTIFIED BY 'sua_senha_aqui';
GRANT ALL PRIVILEGES ON lexxen.* TO 'lexxen_user'@'localhost';
FLUSH PRIVILEGES;

-- Sair do MySQL
EXIT;
```

#### 2.2 Configurar Arquivo .env
```bash
# Copiar arquivo de exemplo
cp .env.example .env

# Editar arquivo .env com suas configurações
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=lexxen
# DB_USERNAME=root
# DB_PASSWORD=sua_senha_aqui
```

### 3. 📥 Instalação do Projeto

#### 3.1 Clonar/Transferir Projeto
```bash
# Opção 1: Se estiver em repositório Git
git clone https://github.com/seu-usuario/lexxen.git
cd lexxen

# Opção 2: Se estiver transferindo arquivos
# Copiar pasta do projeto para o novo PC
# Navegar até a pasta do projeto
cd /caminho/para/lexxen
```

#### 3.2 Instalar Dependências
```bash
# Instalar dependências PHP
composer install

# Instalar dependências Node.js
npm install

# Gerar chave da aplicação
php artisan key:generate

# Configurar cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 3.3 Executar Migrations e Seeders
```bash
# Executar migrations (criar tabelas)
php artisan migrate

# Executar seeders (dados de teste)
php artisan db:seed

# Verificar se tudo foi criado
php artisan migrate:status
```

#### 3.4 Configurar Permissões (Linux)
```bash
# Dar permissões para storage e cache
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 4. 🚀 Iniciar Aplicação

#### 4.1 Servidor de Desenvolvimento
```bash
# Iniciar servidor Laravel
php artisan serve

# Acessar no navegador
# http://localhost:8000
```

#### 4.2 Servidor de Produção (Apache/Nginx)
```bash
# Configurar virtual host apontando para pasta public/
# DocumentRoot: /caminho/para/lexxen/public

# Reiniciar servidor web
sudo systemctl restart apache2  # Apache
sudo systemctl restart nginx    # Nginx
```

### 5. ✅ Verificação da Instalação

#### 5.1 Testar Conexão com Banco
```bash
# Testar conexão
php artisan tinker
>>> DB::connection()->getPdo();
>>> User::count();
>>> ContaBancaria::count();
>>> Carteira::count();
>>> exit
```

#### 5.2 Verificar Dados de Teste
```bash
# Verificar usuários criados
php artisan tinker
>>> User::all(['id', 'email', 'tipo_usuario', 'status_aprovacao']);
>>> exit
```

#### 5.3 Testar Aplicação
```bash
# Acessar URLs principais
# http://localhost:8000 (login)
# http://localhost:8000/dashboard (após login)
# http://localhost:8000/conta (minhas contas)
```

### 6. 🔧 Comandos Úteis para Manutenção

#### 6.1 Comandos de Cache
```bash
# Limpar todos os caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Recriar caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 6.2 Comandos de Banco de Dados
```bash
# Ver status das migrations
php artisan migrate:status

# Reverter última migration
php artisan migrate:rollback

# Reverter todas as migrations
php artisan migrate:reset

# Executar migrations específicas
php artisan migrate --path=/database/migrations/2024_01_01_000000_create_users_table.php

# Executar seeders específicos
php artisan db:seed --class=UserSeeder
```

#### 6.3 Comandos de Debug
```bash
# Ver logs em tempo real
tail -f storage/logs/laravel.log

# Limpar logs
php artisan log:clear

# Ver rotas registradas
php artisan route:list

# Ver configurações
php artisan config:show
```

### 7. 🚨 Solução de Problemas Comuns

#### 7.1 Erro de Permissão (Linux)
```bash
# Problema: Permission denied
# Solução:
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

#### 7.2 Erro de Conexão com Banco
```bash
# Problema: Connection refused
# Solução:
# 1. Verificar se MySQL está rodando
sudo systemctl status mysql

# 2. Verificar configurações no .env
cat .env | grep DB_

# 3. Testar conexão
php artisan tinker
>>> DB::connection()->getPdo();
```

#### 7.3 Erro de Chave de Aplicação
```bash
# Problema: No application encryption key has been specified
# Solução:
php artisan key:generate
php artisan config:cache
```

#### 7.4 Erro de Dependências
```bash
# Problema: Class not found
# Solução:
composer dump-autoload
composer install --no-dev --optimize-autoloader
```

#### 7.5 Erro de Migrations
```bash
# Problema: Migration failed
# Solução:
# 1. Verificar logs
tail -f storage/logs/laravel.log

# 2. Verificar status
php artisan migrate:status

# 3. Executar migrations específicas
php artisan migrate --force
```

### 8. 📦 Backup e Restauração

#### 8.1 Backup do Banco de Dados
```bash
# Criar backup
mysqldump -u root -p lexxen > backup_lexxen_$(date +%Y%m%d_%H%M%S).sql

# Restaurar backup
mysql -u root -p lexxen < backup_lexxen_20241218_143000.sql
```

#### 8.2 Backup dos Arquivos
```bash
# Criar backup da aplicação
tar -czf lexxen_backup_$(date +%Y%m%d_%H%M%S).tar.gz /caminho/para/lexxen

# Excluir node_modules e vendor do backup
tar -czf lexxen_backup_$(date +%Y%m%d_%H%M%S).tar.gz \
    --exclude=node_modules \
    --exclude=vendor \
    --exclude=storage/logs \
    /caminho/para/lexxen
```

### 9. 🌐 Configuração para Produção

#### 9.1 Configurações de Segurança
```bash
# No arquivo .env para produção:
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seudominio.com

# Configurar HTTPS
# Configurar firewall
# Configurar SSL
```

#### 9.2 Otimizações de Performance
```bash
# Otimizar autoloader
composer install --optimize-autoloader --no-dev

# Otimizar caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Otimizar banco de dados
# Adicionar índices necessários
# Configurar query cache
```

---

## 📋 Pré-requisitos (Para Testes)

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

---

## 🚀 CHECKLIST RÁPIDO - INSTALAÇÃO EM NOVO PC

### ✅ Pré-requisitos
- [ ] PHP 8.1+ instalado
- [ ] Composer instalado
- [ ] MySQL/MariaDB instalado
- [ ] Node.js instalado (opcional)
- [ ] Git instalado (opcional)

### ✅ Configuração
- [ ] Banco de dados criado
- [ ] Arquivo .env configurado
- [ ] Projeto transferido/clonado
- [ ] Dependências instaladas (`composer install`)
- [ ] Chave da aplicação gerada (`php artisan key:generate`)

### ✅ Migrations e Dados
- [ ] Migrations executadas (`php artisan migrate`)
- [ ] Seeders executados (`php artisan db:seed`)
- [ ] Conexão com banco testada
- [ ] Dados de teste verificados

### ✅ Aplicação
- [ ] Servidor iniciado (`php artisan serve`)
- [ ] Aplicação acessível (http://localhost:8000)
- [ ] Login funcionando
- [ ] Dashboard carregando
- [ ] Funcionalidades básicas testadas

### ✅ Troubleshooting
- [ ] Logs verificados (storage/logs/laravel.log)
- [ ] Permissões configuradas (Linux)
- [ ] Cache limpo se necessário
- [ ] Dependências atualizadas

---

## 📞 Suporte e Contatos

- **Projeto**: Grigolli Bank
- **Versão**: 1.0
- **Framework**: Laravel 10
- **Banco**: MySQL 8.0+
- **Última atualização**: 18/12/2024

### 🔗 Links Úteis
- [Laravel Documentation](https://laravel.com/docs)
- [Composer Documentation](https://getcomposer.org/doc/)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Bootstrap Documentation](https://getbootstrap.com/docs/)

---

**✅ Sistema pronto para uso em produção!**

