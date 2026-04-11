# 💰 Sistema de Controle de Despesas

Aplicação web desenvolvida em PHP para gerenciamento de gastos pessoais.  
Permite que usuários façam login e gerenciem suas despesas de forma simples e organizada.

---

## 📌 Visão Geral

Este sistema permite:

- Registrar despesas com descrição, valor e data
- Editar despesas existentes
- Excluir despesas
- Visualizar todas as despesas cadastradas
- Calcular automaticamente o total de gastos
- Acessar o sistema com autenticação (login)

---

## 🖥️ Interface

A aplicação possui interface gráfica (GUI), acessada via navegador, contendo:

- Formulário de cadastro e edição
- Tabela de listagem de despesas
- Botões de ação (editar e excluir)
- Tela de login

---

## 🛠️ Tecnologias Utilizadas

- PHP
- PostgreSQL
- HTML
- CSS
- GitHub Actions (CI)

---

## 📁 Estrutura do Projeto

```
controleDespesas/
│
├── .github/
│ └── workflows/
│ └── ci.yml
│
├── src/
│ ├── conexao.php
│ ├── inserir.php
│ ├── editar.php
│ ├── excluir.php
│
├── tests/
│ └── testes.php
│
├── index.php
├── login.php
├── logout.php
├── lint.bat
└── README.md
```

---

## ⚙️ Pré-requisitos

- XAMPP (ou outro servidor com PHP)
- PostgreSQL instalado
- Navegador web

---

## 🗄️ Configuração do Banco de Dados

### 1. Criar o banco

CREATE DATABASE controle_despesas;

---

### 2. Criar as tabelas
```
CREATE TABLE usuarios (
    id SERIAL PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    senha VARCHAR(255) NOT NULL
);
```
```
CREATE TABLE despesas (
    id SERIAL PRIMARY KEY,
    descricao VARCHAR(255) NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    data DATE
);
```
---

### 3. Inserir usuário de teste

INSERT INTO usuarios (email, senha)
VALUES ('admin@email.com', md5('123456'));

---

## 🔐 Acesso ao Sistema

Atualmente, o sistema não possui cadastro de usuários pela interface.

Para acessar, utilize:

- **Email:** admin@email.com  
- **Senha:** 123456  

⚠️ O acesso só é possível com usuários cadastrados no banco.

---

## 🚀 Como Executar o Projeto

### 1. Clonar o repositório

git clone https://github.com/maria-morena/controleDespesas.git

---

### 2. Colocar na pasta do servidor

C:\xampp\htdocs\

---

### 3. Configurar conexão com o banco

Arquivo: `src/conexao.php`

Exemplo:

$host = "localhost";  
$db = "controle_despesas";  
$user = "postgres";  
$senha = "sua_senha";

---

### 4. Iniciar o servidor

Abra o XAMPP e inicie o Apache.

---

### 5. Acessar no navegador

http://localhost/controleDespesas/login.php

---

## 🧪 Testes Automatizados

Cenários testados:

- Caminho feliz (valor positivo)
- Valor negativo (inválido)
- Valor zero (caso limite)
- Descrição vazia (inválido)
- Valor muito alto (caso extremo)

### Executar testes

C:\xampp\php\php.exe tests/testes.php

---

## 🔍 Lint (Verificação de Código)

Execute:

lint.bat

---

## ⚙️ Integração Contínua (CI)

Executa automaticamente:

- Verificação de sintaxe (lint)
- Testes automatizados

Em:

- push
- pull request

Arquivo:

.github/workflows/ci.yml

---

## ⚠️ Possíveis Problemas

### PHP não reconhecido

Use:

C:\xampp\php\php.exe

---

### Erro no banco

Verifique:

- credenciais no `conexao.php`
- se o PostgreSQL está ativo

---

## 📌 Limitações

- Uso de MD5 (não recomendado em produção)
- Sem cadastro de usuários via interface
- Sem separação por usuário

---

## 🚀 Melhorias Futuras

- password_hash
- Cadastro de usuários
- Filtro por data
- Dashboard com gráficos

---

## 👩‍💻 Autora

Maria Morena

---

## 📄 Licença

Projeto acadêmico para fins educacionais.
