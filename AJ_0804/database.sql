-- ============================================================
--  SCRIPT DE CRIAÇÃO DO BANCO DE DADOS - Cadastro de Funcionários
--  Compatível com PostgreSQL
-- ============================================================

-- 1. Criar o banco (execute como superusuário, fora de uma transação)
-- CREATE DATABASE cadastro_funcionarios ENCODING 'UTF8';

-- 2. Conectar ao banco e executar o restante:
-- \c cadastro_funcionarios

-- ============================================================
-- TABELA: funcionarios
-- ============================================================
CREATE TABLE IF NOT EXISTS funcionarios (
    id        SERIAL       PRIMARY KEY,
    nome      VARCHAR(150) NOT NULL,
    cargo     VARCHAR(100) NOT NULL,
    email     VARCHAR(200) NOT NULL,
    telefone  VARCHAR(20),
    ativo     BOOLEAN      NOT NULL DEFAULT TRUE,
    criado_em TIMESTAMP    NOT NULL DEFAULT NOW()
);

-- ============================================================
-- TABELA: usuarios  (autenticação do login)
-- ============================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id    SERIAL       PRIMARY KEY,
    login VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL   -- hash SHA-256
);

-- Usuário padrão: admin / admin123
-- Senha gerada com: echo -n 'admin123' | sha256sum
INSERT INTO usuarios (login, senha)
VALUES ('admin', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9')
ON CONFLICT DO NOTHING;

-- ============================================================
-- INSERTs de exemplo para a tabela funcionarios
-- ============================================================
INSERT INTO funcionarios (nome, cargo, email, telefone, ativo) VALUES
    ('João Silva',    'Administrador', 'jo@mi@ensx.com',      '(11) 91234-5678', TRUE),
    ('Ana Mendes',    'Gerente',       'repca@ensx.com',      '(11) 92345-6789', TRUE),
    ('Pedro Souza',   'Assistente',    'souza@ensx.com',      '(21) 93456-7890', TRUE),
    ('Carla Oliveira','Administrador', 'robog@ensx.com',      '(31) 94567-8901', TRUE),
    ('Lucas Martins', 'Assistente',    'lucas@ensx.com',      '(41) 95678-9012', FALSE),
    ('Fernanda Lima', 'Gerente',       'fernanda@ensx.com',   '(51) 96789-0123', TRUE),
    ('Ricardo Nunes', 'Desenvolvedor', 'ricardo@ensx.com',    '(61) 97890-1234', TRUE);
    ('Patrícia Costa','Analista',      'patricia@ensx.com',   '(71) 98901-2345', FALSE),
    ('Bruno Alves',   'Desenvolvedor', 'bruno@ensx.com',      '(81) 99012-3456', TRUE),
    ('Juliana Ramos', 'Analista',      'juliana@ensx.com',    '(91) 90123-4567', TRUE);