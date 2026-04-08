<?php
// navbar.php - Barra de navegação reutilizável
// Variável $pagina_atual deve ser definida antes de incluir este arquivo
// Exemplo: $pagina_atual = 'listagem';
if (!isset($pagina_atual)) $pagina_atual = '';
?>
<nav class="navbar">
    <a href="listagem.php" class="navbar-brand">
        <!-- Globe / world icon -->
        <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" fill="white">
            <circle cx="16" cy="16" r="13" stroke="white" stroke-width="2" fill="none"/>
            <ellipse cx="16" cy="16" rx="5.5" ry="13" stroke="white" stroke-width="2" fill="none"/>
            <line x1="3" y1="16" x2="29" y2="16" stroke="white" stroke-width="2"/>
            <line x1="5" y1="10" x2="27" y2="10" stroke="white" stroke-width="2"/>
            <line x1="5" y1="22" x2="27" y2="22" stroke="white" stroke-width="2"/>
        </svg>
        Cadastro de Funcionários
    </a>

    <ul class="navbar-menu">
        <li><a href="listagem.php" class="<?php echo $pagina_atual === 'inicio' ? 'active' : ''; ?>">Início</a></li>
        <li><a href="listagem.php" class="<?php echo $pagina_atual === 'listagem' ? 'active' : ''; ?>">Listagem</a></li>
    </ul>

    <div class="navbar-right">
        Olá,&nbsp;<strong><?php echo isset($_SESSION['usuario_logado']) ? htmlspecialchars($_SESSION['usuario_logado']) : 'Admin'; ?></strong>
        &nbsp;▾
        &nbsp;|&nbsp;
        <a href="logout.php" style="color:#fff;font-size:12px;">Sair</a>
    </div>
</nav>