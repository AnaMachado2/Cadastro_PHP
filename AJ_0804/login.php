<?php
// login.php
session_start();

// Se já estiver logado, redireciona
if (!empty($_SESSION['usuario_logado'])) {
    header('Location: listagem.php');
    exit;
}

require_once 'conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = isset($_POST['login']) ? trim($_POST['login']) : '';
    $senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';

    if ($login === '' || $senha === '') {
        $erro = 'Preencha usuário e senha.';
    } else {
        // Senha comparada como SHA-256
        $hash = hash('sha256', $senha);
        $pdo  = getConexao();
        $stmt = $pdo->prepare('SELECT id, login FROM usuarios WHERE login = :login AND senha = :senha LIMIT 1');
        $stmt->execute(array(':login' => $login, ':senha' => $hash));
        $usuario = $stmt->fetch();

        if ($usuario) {
            $_SESSION['usuario_logado'] = $usuario['login'];
            $_SESSION['usuario_id']     = $usuario['id'];
            header('Location: listagem.php');
            exit;
        } else {
            $erro = 'Usuário ou senha inválidos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Cadastro de Funcionários</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login-wrapper">
    <div class="login-card">

        <div class="login-header">
            <!-- Avatar SVG -->
            <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                <circle cx="32" cy="20" r="14"/>
                <path d="M4 56c0-15.464 12.536-28 28-28s28 12.536 28 28H4z"/>
            </svg>
            <h1>Cadastro de Funcionários</h1>
        </div>

        <?php if ($erro): ?>
            <div class="login-error"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="login-field">
                <span class="login-field-icon">
                    <!-- User icon -->
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                    </svg>
                </span>
                <input type="text" name="login" placeholder="Usuário" autocomplete="username"
                       value="<?php echo isset($_POST['login']) ? htmlspecialchars($_POST['login']) : ''; ?>">
            </div>

            <div class="login-field">
                <span class="login-field-icon">
                    <!-- Lock icon -->
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 1C9.24 1 7 3.24 7 6v2H5c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2h-2V6c0-2.76-2.24-5-5-5zm0 2c1.66 0 3 1.34 3 3v2H9V6c0-1.66 1.34-3 3-3zm1 11.73V17h-2v-2.27c-.6-.34-1-.98-1-1.73 0-1.1.9-2 2-2s2 .9 2 2c0 .75-.4 1.39-1 1.73z"/>
                    </svg>
                </span>
                <input type="password" name="senha" placeholder="Senha" autocomplete="current-password">
            </div>

            <button type="submit" class="login-btn">Entrar</button>
        </form>

        <hr class="login-divider">
        <div class="login-forgot">
            <a href="#">Esqueci minha senha</a>
        </div>

    </div>
</div>
</body>
</html>