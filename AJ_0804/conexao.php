<?php
function getConexao() {
    static $pdo = null;
    if ($pdo === null) {
        // Configurações do seu banco de dados
        $host = 'localhost';
        $db   = 'cadastro_funcionarios';
        $user = 'postgres'; // usuário padrão
        $pass = '123456';
        $port = '5432'; // porta padrão do PostgreSQL

        try {
            // String de conexão (DSN)
            $dsn = "pgsql:host=$host;port=$port;dbname=$db;";
            
            // Criando a instância do PDO
            $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        } catch (PDOException $e) {
            // Caso ocorra erro, exibe a mensagem
            die("Erro ao conectar: " . $e->getMessage());
        }
    }
    return $pdo;
}
?>