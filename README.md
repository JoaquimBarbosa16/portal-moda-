# 👗 Portal Vogue BR — Sistema de Notícias de Moda e Estilo

Sistema web desenvolvido em **PHP + MySQL** para gerenciamento de notícias focadas em **moda, tendências e estilo**, com controle de acesso entre **Administrador** e **Repórteres**.

---

## 🚀 Funcionalidades

### 👑 Administrador

* Excluir ou Editar cadastro de repórteres
* Acessar painel administrativo
* Gerenciar usuários
* Visualizar e acompanhar todas as notícias

### 🧑‍💻 Repórter

* Criar notícias sobre moda e estilo
* Editar suas próprias publicações
* Excluir suas notícias
* Atualizar perfil

---

## 🔐 Sistema de Acesso

* Login com senha criptografada (`password_hash`)
* Validação com `password_verify`
* Controle de sessão via PHP

### 🎯 Permissões de Usuário:

* **admin** → acesso total
* **reporter** → acesso restrito

### 📌 Status de Usuário:

* **ativo** → acesso liberado
* **pendente** → aguardando aprovação do admin

---

---

## 👤 Usuário padrão (Admin)

* **Email:** [admin@email.com](mailto:admin@email.com)
* **Senha:** 123

---

## ⚙️ Tecnologias Utilizadas

* PHP (procedural)
* MySQL
* HTML5
* CSS3
* XAMPP (ambiente local)

---

## 🧪 Como Rodar o Projeto



```
htdocs/
```

Inicie:

* Apache
* MySQL

4. Importe o banco de dados (voguebr_dump.sql) no phpMyAdmin

5. Acesse no navegador:

```
http://localhost/fashion
```

---

## 🔒 Segurança Implementada

* Uso de `password_hash()` e `password_verify()`
* Proteção de rotas com `verifica_login.php`
* Validação de dados de entrada
* Controle de sessão
* Restrição de acesso por tipo de usuário

---

## 🎨 Tema do Projeto

O sistema é voltado para o universo de **moda e estilo**, permitindo a publicação de conteúdos como:

* Tendências da estação
* Dicas de estilo
* Cobertura de eventos fashion
* Novidades do mundo da moda

---

## 👨‍💻 Autor

**Joaquim Barbosa**

---

## 📌 Observações

* Apenas usuários aprovados podem acessar o sistema
* Repórteres não têm acesso às funções administrativas
* Todas as ações são controladas por sessão
* Sistema pensado para organização e publicação de conteúdo fashion

---
