<?php
// listagem.php
session_start();
if (empty($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit;
}

require_once 'conexao.php';

$pagina_atual = 'listagem';

// Paginação
$por_pagina = 5;
$pagina     = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$offset     = ($pagina - 1) * $por_pagina;

// Busca
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

// Flash message (vindo de processar.php)
$flash = '';
if (!empty($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

$pdo = getConexao();

try {
    if ($busca !== '') {
        $like = '%' . $busca . '%';
        $total_stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM funcionarios
             WHERE nome ILIKE :b OR cargo ILIKE :b2 OR email ILIKE :b3"
        );
        $total_stmt->execute(array(':b' => $like, ':b2' => $like, ':b3' => $like));
        $total = (int)$total_stmt->fetchColumn();

        $stmt = $pdo->prepare(
            "SELECT * FROM funcionarios
             WHERE nome ILIKE :b OR cargo ILIKE :b2 OR email ILIKE :b3
             ORDER BY id ASC
             LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':b',      $like,       PDO::PARAM_STR);
        $stmt->bindValue(':b2',     $like,       PDO::PARAM_STR);
        $stmt->bindValue(':b3',     $like,       PDO::PARAM_STR);
        $stmt->bindValue(':limit',  $por_pagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,     PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $total_stmt = $pdo->query("SELECT COUNT(*) FROM funcionarios");
        $total      = (int)$total_stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT * FROM funcionarios ORDER BY id ASC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit',  $por_pagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,     PDO::PARAM_INT);
        $stmt->execute();
    }

    $funcionarios = $stmt->fetchAll();
} catch (PDOException $e) {
    $funcionarios = array();
    $total        = 0;
    $flash        = 'Erro ao buscar funcionários: ' . $e->getMessage();
}

$total_paginas = max(1, ceil($total / $por_pagina));

// Monta query string para links de paginação
function paginaUrl($p, $busca) {
    $q = array('pagina' => $p);
    if ($busca !== '') $q['busca'] = $busca;
    return 'listagem.php?' . http_build_query($q);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Funcionários</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="page-wrapper">
    <h2 class="page-title">Listagem de Funcionários</h2>

    <?php if ($flash): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($flash); ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">

            <!-- Toolbar: busca + botão novo -->
            <div class="table-toolbar">
                <form method="GET" action="listagem.php" class="search-wrapper">
                    <div class="search-input-wrap">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.5 14h-.79l-.28-.27A6.47 6.47 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                        </svg>
                        <input type="text" name="busca" placeholder="Buscar funcionário..."
                               value="<?php echo htmlspecialchars($busca); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Pesquisar</button>
                </form>

                <a href="cadastro.php" class="btn btn-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="white"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                    Novo Funcionário
                </a>
            </div>

            <!-- Tabela -->
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Cargo</th>
                            <th>E-mail</th>
                            <th>Situação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($funcionarios)): ?>
                            <tr>
                                <td colspan="6" style="text-align:center;padding:24px;color:#999;">
                                    Nenhum funcionário encontrado.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($funcionarios as $i => $f): ?>
                                <?php
                                $num   = $offset + $i + 1;
                                $ativo = ($f['ativo'] === 't' || $f['ativo'] === true || $f['ativo'] === 1);
                                ?>
                                <tr>
                                    <td><?php echo $num; ?>.</td>
                                    <td><?php echo htmlspecialchars($f['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($f['cargo']); ?></td>
                                    <td><em><?php echo htmlspecialchars($f['email']); ?></em></td>
                                    <td>
                                        <span class="badge <?php echo $ativo ? 'badge-ativo' : 'badge-inativo'; ?>">
                                            <?php echo $ativo ? 'Ativo' : 'Inativo'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <!-- Editar -->
                                            <a href="cadastro.php?id=<?php echo $f['id']; ?>"
                                               class="btn-edit" title="Editar">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="white">
                                                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zm17.71-10.21a1 1 0 000-1.41l-2.34-2.34a1 1 0 00-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                                </svg>
                                            </a>
                                            <!-- Detalhes -->
                                            <a href="detalhe.php?id=<?php echo $f['id']; ?>"
                                               class="btn-view" title="Detalhes">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="white">
                                                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zm0 12.5a5 5 0 110-10 5 5 0 010 10zm0-8a3 3 0 100 6 3 3 0 000-6z"/>
                                                </svg>
                                            </a>
                                            <!-- Excluir -->
                                            <a href="processar.php?acao=excluir&id=<?php echo $f['id']; ?><?php echo $busca ? '&busca='.urlencode($busca) : ''; ?>"
                                               class="btn-del" title="Excluir"
                                               onclick="return confirm('Confirma a exclusão de <?php echo htmlspecialchars(addslashes($f['nome'])); ?>?');">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="white">
                                                    <path d="M16 9v10H8V9h8m-1.5-6h-5l-1 1H5v2h14V4h-3.5l-1-1zm3.5 4H6v12a2 2 0 002 2h8a2 2 0 002-2V7z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div><!-- /table-wrap -->

            <!-- Paginação -->
            <?php if ($total_paginas > 1): ?>
            <div class="pagination">
                <?php if ($pagina > 1): ?>
                    <a href="<?php echo paginaUrl($pagina - 1, $busca); ?>">&laquo;</a>
                <?php else: ?>
                    <span class="disabled">&laquo;</span>
                <?php endif; ?>

                <?php
                $inicio_pg = max(1, $pagina - 2);
                $fim_pg    = min($total_paginas, $pagina + 2);
                if ($inicio_pg > 1): ?>
                    <a href="<?php echo paginaUrl(1, $busca); ?>">1</a>
                    <?php if ($inicio_pg > 2): ?><span class="disabled">…</span><?php endif; ?>
                <?php endif; ?>

                <?php for ($p = $inicio_pg; $p <= $fim_pg; $p++): ?>
                    <?php if ($p === $pagina): ?>
                        <span class="active"><?php echo $p; ?></span>
                    <?php else: ?>
                        <a href="<?php echo paginaUrl($p, $busca); ?>"><?php echo $p; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($fim_pg < $total_paginas): ?>
                    <?php if ($fim_pg < $total_paginas - 1): ?><span class="disabled">…</span><?php endif; ?>
                    <a href="<?php echo paginaUrl($total_paginas, $busca); ?>"><?php echo $total_paginas; ?></a>
                <?php endif; ?>

                <?php if ($pagina < $total_paginas): ?>
                    <a href="<?php echo paginaUrl($pagina + 1, $busca); ?>">Próximo &raquo;</a>
                <?php else: ?>
                    <span class="disabled">Próximo &raquo;</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div><!-- /card-body -->
    </div><!-- /card -->
</div>

</body>
</html>