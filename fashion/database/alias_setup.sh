#!/bin/bash
# ============================================================
#  VOGUE BR — Script de Alias para o Banco de Dados MySQL
#  Execute: source database/alias_setup.sh
#  Ou adicione ao seu ~/.bashrc ou ~/.zshrc
# ============================================================

# ─── CONFIGURAÇÕES (edite aqui) ──────────────────────────────
DB_HOST="localhost"
DB_PORT="3306"
DB_NAME="voguebr"
DB_USER="root"
DB_PASS=""          # deixe vazio se não tiver senha
# ─────────────────────────────────────────────────────────────

# Helper interno para montar string de conexão
_vogue_conn() {
  local args="-h${DB_HOST} -P${DB_PORT} -u${DB_USER}"
  [[ -n "$DB_PASS" ]] && args="${args} -p${DB_PASS}"
  echo "${args} ${DB_NAME}"
}

# ─── ALIASES PRINCIPAIS ──────────────────────────────────────

# Abrir shell MySQL interativo no banco voguebr
alias vogue-db='mysql -h${DB_HOST} -P${DB_PORT} -u${DB_USER} ${DB_PASS:+-p${DB_PASS}} ${DB_NAME}'

# Criar o banco do zero (importar dump)
alias vogue-import='mysql -h${DB_HOST} -P${DB_PORT} -u${DB_USER} ${DB_PASS:+-p${DB_PASS}} < database/voguebr_dump.sql && echo "✓ Banco importado com sucesso!"'

# Fazer dump completo (backup)
alias vogue-dump='mysqldump -h${DB_HOST} -P${DB_PORT} -u${DB_USER} ${DB_PASS:+-p${DB_PASS}} --single-transaction --routines --triggers ${DB_NAME} > database/backup_$(date +%Y%m%d_%H%M%S).sql && echo "✓ Backup gerado em database/"'

# Dump só da estrutura (sem dados)
alias vogue-schema='mysqldump -h${DB_HOST} -P${DB_PORT} -u${DB_USER} ${DB_PASS:+-p${DB_PASS}} --no-data --routines ${DB_NAME} > database/schema_only.sql && echo "✓ Schema exportado"'

# Dump só dos dados (sem estrutura)
alias vogue-data='mysqldump -h${DB_HOST} -P${DB_PORT} -u${DB_USER} ${DB_PASS:+-p${DB_PASS}} --no-create-info ${DB_NAME} > database/data_only.sql && echo "✓ Dados exportados"'

# Listar tabelas
alias vogue-tables='mysql -h${DB_HOST} -P${DB_PORT} -u${DB_USER} ${DB_PASS:+-p${DB_PASS}} ${DB_NAME} -e "SHOW TABLES;"'

# Ver usuários cadastrados
alias vogue-users='mysql -h${DB_HOST} -P${DB_PORT} -u${DB_USER} ${DB_PASS:+-p${DB_PASS}} ${DB_NAME} -e "SELECT id, name, email, role, active, DATE_FORMAT(created_at,\"%d/%m/%Y\") AS cadastro FROM users ORDER BY id;"'

# Ver notícias
alias vogue-news='mysql -h${DB_HOST} -P${DB_PORT} -u${DB_USER} ${DB_PASS:+-p${DB_PASS}} ${DB_NAME} -e "SELECT id, SUBSTR(title,1,50) AS titulo, category, status, DATE_FORMAT(created_at,\"%d/%m/%Y\") AS data FROM news ORDER BY id;"'

# Contar registros por tabela
alias vogue-count='mysql -h${DB_HOST} -P${DB_PORT} -u${DB_USER} ${DB_PASS:+-p${DB_PASS}} ${DB_NAME} -e "SELECT \"users\" AS tabela, COUNT(*) AS total FROM users UNION SELECT \"news\", COUNT(*) FROM news UNION SELECT \"categories\", COUNT(*) FROM categories;"'

# Resetar o banco (DROP + reimportar)
alias vogue-reset='echo "⚠ Resetando banco voguebr..." && mysql -h${DB_HOST} -P${DB_PORT} -u${DB_USER} ${DB_PASS:+-p${DB_PASS}} -e "DROP DATABASE IF EXISTS voguebr;" && mysql -h${DB_HOST} -P${DB_PORT} -u${DB_USER} ${DB_PASS:+-p${DB_PASS}} < database/voguebr_dump.sql && echo "✓ Banco resetado com sucesso!"'

# Adicionar usuário manualmente via CLI
vogue-add-user() {
  if [[ $# -lt 3 ]]; then
    echo "Uso: vogue-add-user <nome> <email> <senha> [role=user|admin]"
    return 1
  fi
  local NAME="$1" EMAIL="$2" PASS="$3" ROLE="${4:-user}"
  local HASH=$(php -r "echo password_hash('${PASS}', PASSWORD_DEFAULT);")
  mysql -h${DB_HOST} -P${DB_PORT} -u${DB_USER} ${DB_PASS:+-p${DB_PASS}} ${DB_NAME} \
    -e "INSERT INTO users (name,email,password,role) VALUES ('${NAME}','${EMAIL}','${HASH}','${ROLE}');" \
  && echo "✓ Usuário '${NAME}' criado com role '${ROLE}'!" \
  || echo "✗ Falha — e-mail pode já estar cadastrado."
}

# Alterar senha de usuário via CLI
vogue-set-pass() {
  if [[ $# -lt 2 ]]; then echo "Uso: vogue-set-pass <email> <nova_senha>"; return 1; fi
  local EMAIL="$1" PASS="$2"
  local HASH=$(php -r "echo password_hash('${PASS}', PASSWORD_DEFAULT);")
  mysql -h${DB_HOST} -P${DB_PORT} -u${DB_USER} ${DB_PASS:+-p${DB_PASS}} ${DB_NAME} \
    -e "UPDATE users SET password='${HASH}' WHERE email='${EMAIL}';" \
  && echo "✓ Senha de '${EMAIL}' atualizada!" \
  || echo "✗ Falha ao atualizar."
}

# Status do banco
vogue-status() {
  echo ""
  echo "═══════════════════════════════════════"
  echo "  VOGUE BR — Status do Banco"
  echo "═══════════════════════════════════════"
  echo "  Host:  ${DB_HOST}:${DB_PORT}"
  echo "  Banco: ${DB_NAME}"
  echo "  User:  ${DB_USER}"
  echo "───────────────────────────────────────"
  mysql -h${DB_HOST} -P${DB_PORT} -u${DB_USER} ${DB_PASS:+-p${DB_PASS}} ${DB_NAME} 2>/dev/null \
    -e "SELECT CONCAT('  Usuários:  ', COUNT(*)) FROM users;
        SELECT CONCAT('  Notícias:  ', COUNT(*)) FROM news;
        SELECT CONCAT('  Publicadas: ', SUM(status=''published'')) FROM news;" 2>/dev/null \
  || echo "  ✗ Não foi possível conectar ao banco."
  echo "═══════════════════════════════════════"
  echo ""
}

echo ""
echo "✦ VOGUE BR — Aliases carregados!"
echo ""
echo "  vogue-db          → Abrir shell MySQL"
echo "  vogue-import      → Importar dump SQL"
echo "  vogue-dump        → Fazer backup"
echo "  vogue-tables      → Listar tabelas"
echo "  vogue-users       → Ver usuários"
echo "  vogue-news        → Ver notícias"
echo "  vogue-count       → Contar registros"
echo "  vogue-reset       → Resetar banco"
echo "  vogue-add-user    → Criar usuário via CLI"
echo "  vogue-set-pass    → Alterar senha via CLI"
echo "  vogue-status      → Status do banco"
echo ""
