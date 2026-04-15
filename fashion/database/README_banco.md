# VOGUE BR — Guia do Banco de Dados

## 1. Criar o banco (primeira vez)

```bash
# Entre no MySQL e execute o dump:
mysql -u root -p < database/voguebr_dump.sql

# Ou em uma linha com senha:
mysql -u root -pSUASENHA < database/voguebr_dump.sql
```

## 2. Configurar credenciais no PHP

Edite o arquivo `includes/config.php` e ajuste:

```php
define('DB_HOST', 'localhost');   // host do MySQL
define('DB_PORT', '3306');        // porta (padrão 3306)
define('DB_NAME', 'voguebr');     // nome do banco
define('DB_USER', 'root');        // usuário
define('DB_PASS', '');            // senha (vazia se não tiver)
```

**Ou via variáveis de ambiente (recomendado em produção):**
```bash
export DB_HOST=localhost
export DB_USER=voguebr_user
export DB_PASS=minha_senha_segura
export DB_NAME=voguebr
```

## 3. Carregar os aliases (terminal)

```bash
# Carrega temporariamente (sessão atual):
source database/alias_setup.sh

# Carrega permanentemente (adicione ao ~/.bashrc ou ~/.zshrc):
echo 'source /caminho/para/fashion/database/alias_setup.sh' >> ~/.bashrc
source ~/.bashrc
```

## 4. Aliases disponíveis

| Alias | O que faz |
|-------|-----------|
| `vogue-db` | Abre o shell MySQL interativo no banco voguebr |
| `vogue-import` | Importa o dump SQL (cria tabelas + dados) |
| `vogue-dump` | Gera backup completo com timestamp |
| `vogue-schema` | Exporta só a estrutura (sem dados) |
| `vogue-data` | Exporta só os dados (sem estrutura) |
| `vogue-tables` | Lista todas as tabelas |
| `vogue-users` | Mostra todos os usuários cadastrados |
| `vogue-news` | Mostra todas as notícias |
| `vogue-count` | Conta registros por tabela |
| `vogue-reset` | ⚠ Apaga e recria o banco do zero |
| `vogue-status` | Mostra status e contagens do banco |

### Funções com argumentos:

```bash
# Criar usuário via terminal:
vogue-add-user "Maria Silva" "maria@email.com" "senha123" user
vogue-add-user "João Admin" "joao@vogue.com" "senha456" admin

# Alterar senha via terminal:
vogue-set-pass "ana@gmail.com" "nova_senha"
```

## 5. Cadastrar novos usuários

### Via portal web:
Acesse `http://localhost:8080/register.php`
- Preencha nome, e-mail, senha e confirmação
- O registro vai direto para a tabela `users` no MySQL
- O usuário é logado automaticamente após o cadastro

### Via painel admin:
Acesse `http://localhost:8080/admin-users.php` (requer login admin)
- Clique em "+ Novo Usuário"
- Defina nome, e-mail, senha e perfil (user/admin)

### Via terminal (alias):
```bash
vogue-add-user "Nome" "email@exemplo.com" "senha" user
```

### Via MySQL diretamente:
```sql
INSERT INTO users (name, email, password, role)
VALUES ('Nome', 'email@ex.com', '$2y$...hash...', 'user');
```

## 6. Estrutura das tabelas

```
users
  id, name, email, password (bcrypt), role, bio, avatar, active, created_at, updated_at

news
  id, author_id (→users), title, slug, category, status, excerpt, content, image_url, views, published_at, created_at, updated_at

categories
  id, name, slug, description, color, created_at

user_sessions
  id, user_id (→users), token, ip, user_agent, created_at, expires_at
```

## 7. Rodar o projeto

```bash
cd fashion/
php -S localhost:8080
# Acesse: http://localhost:8080
```

## 8. Contas padrão

| Perfil | E-mail | Senha |
|--------|--------|-------|
| Admin | admin@vogue.com | admin123 |
| Usuária | ana@gmail.com | ana123 |
| Usuária | bea@gmail.com | bea123 |
| Usuária | carla@gmail.com | carla123 |
