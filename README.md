# Sistema de Cadastro de Funcionários - PHP & PostgreSQL

Este projeto consiste em um mini sistema web para **Cadastro de Funcionários**, desenvolvido como parte de uma atividade acadêmica. O sistema foca em operações essenciais de backend com PHP 5 e persistência de dados em PostgreSQL, seguindo um design fiel às especificações visuais fornecidas.

## 🚀 Tecnologias Utilizadas

* **Backend:** PHP 5 (Puro, sem frameworks).
* **Banco de Dados:** PostgreSQL.
* **Frontend:** HTML5 e CSS3 (Design Responsivo e Customizado).
* **Ferramentas:** VS Code, pgAdmin e Servidor Apache (XAMPP/WAMP).

---

## 📋 Funcionalidades

1.  **Autenticação:** Tela de login para acesso administrativo.
2.  **Cadastro:** Formulário completo para inclusão de novos funcionários.
3.  **Listagem:** Visualização de dados em tabela com indicadores de status (Ativo/Inativo).
4.  **Busca:** Filtro em tempo real para localizar funcionários na base de dados.

---

## 🛠️ Configuração do Banco de Dados

Para rodar o projeto, utilize o script SQL abaixo no seu pgAdmin para criar a estrutura necessária:

```sql
CREATE DATABASE telas;

CREATE TABLE funcionarios (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cargo VARCHAR(50),
    email VARCHAR(100),
    telefone VARCHAR(20),
    situacao BOOLEAN DEFAULT true
);
