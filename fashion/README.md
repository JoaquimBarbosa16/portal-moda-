# VOGUE BR — Fashion Portal (PHP)

## 📦 Estrutura do Projeto

```
fashion/
├── index.php            ← Login
├── register.php         ← Cadastro
├── logout.php           ← Sair
├── dashboard.php        ← Dashboard do usuário
├── news.php             ← Listagem de notícias
├── news-view.php        ← Ver notícia
├── news-create.php      ← Criar/Editar notícia
├── news-delete.php      ← Excluir notícia
├── profile.php          ← Perfil do usuário
├── admin.php            ← Painel Admin
├── admin-users.php      ← CRUD de Usuários (Admin)
├── admin-news.php       ← CRUD de Notícias (Admin)
├── includes/
│   ├── config.php       ← Config + funções + seed de dados
│   ├── header.php       ← Header + sidebar + CSS global
│   └── footer.php       ← Footer + modal confirmar
└── data/                ← Criado automaticamente
    ├── users.json       ← Banco de usuários
    └── news.json        ← Banco de notícias
```

## 🚀 Como Rodar

### Opção 1 — PHP Built-in Server (mais fácil)
```bash
cd fashion
php -S localhost:8080
# Acesse: http://localhost:8080
```

### Opção 2 — XAMPP / WAMP / Laragon
1. Copie a pasta `fashion/` para `htdocs/` (XAMPP) ou `www/` (WAMP)
2. Acesse: `http://localhost/fashion/`

### Opção 3 — Docker
```bash
docker run -p 8080:80 -v $(pwd)/fashion:/var/www/html php:8.2-apache
```

## 🔐 Contas de Acesso

| Perfil | E-mail | Senha |
|--------|--------|-------|
| Admin  | admin@vogue.com | admin123 |
| Usuária | ana@gmail.com | ana123 |
| Usuária | bea@gmail.com | bea123 |

## ✅ Funcionalidades

### Usuário comum
- Login e cadastro
- Dashboard com estatísticas
- Listagem de notícias com busca e filtro por categoria
- Visualização completa de notícias com imagem
- Criar, editar e excluir notícias
- Edição de perfil (nome, e-mail, senha, bio)

### Admin (admin@vogue.com)
- Tudo acima +
- Painel Admin com visão geral
- CRUD completo de Usuários (listar, criar, editar, excluir)
- CRUD completo de Notícias com toggle rápido de status
- Ver resumo de usuários recentes e notícias recentes

## 🗄️ Banco de Dados
Usa arquivos JSON em `/data/` — não precisa de MySQL.
Os dados são criados automaticamente ao primeiro acesso.
Para resetar: delete os arquivos `data/users.json` e `data/news.json`.

## 📋 Requisitos
- PHP 8.0+
- Extensão `json` (padrão em qualquer instalação PHP)
