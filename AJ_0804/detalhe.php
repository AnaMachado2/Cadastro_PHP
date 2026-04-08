<?php
// detalhe.php — Exibe os detalhes de um funcionário
session_start();
if (empty($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit;
}

require_once 'conexao.php';

$pagina_atual = 'listagem';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    header('Location: listagem.php');
    exit;
}

$pdo  = getConexao();
$stmt = $pdo->prepare('SELECT * FROM funcionarios WHERE id = :id LIMIT 1');
$stmt->execute(array(':id' => $id));
$f = $stmt->fetch();

if (!$f) {
    header('Location: listagem.php');
    exit;
}

$ativo = ($f['ativo'] === 't' || $f['ativo'] === true || $f['ativo'] === 1);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhe do Funcionário</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .detail-table { width: 100%; border-collapse: collapse; }
        .detail-table th { width: 180px; text-align: left; padding: 10px 14px; background: #f4f6fb; color: #555; font-weight: 600; font-size: 12px; text-transform: uppercase; border-bottom: 1px solid #eee; }
        .detail-table td { padding: 10px 14px; border-bottom: 1px solid #eee; color: #333; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="page-wrapper">
    <h2 class="page-title">Detalhe do Funcionário</h2>

    <div class="card">
        <div class="card-header">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
            </svg>
            Informações do Funcionário
        </div>
        <div class="card-body" style="padding:0;">
            <table class="detail-table">
                <tr><th>ID</th><td><?php echo (int)$f['id']; ?></td></tr>
                <tr><th>Nome</th><td><?php echo htmlspecialchars($f['nome']); ?></td></tr>
                <tr><th>Cargo</th><td><?php echo htmlspecialchars($f['cargo']); ?></td></tr>
                <tr><th>E-mail</th><td><?php echo htmlspecialchars($f['email']); ?></td></tr>
                <tr><th>Telefone</th><td><?php echo $f['telefone'] ? htmlspecialchars($f['telefone']) : '<span class="text-muted">—</span>'; ?></td></tr>
                <tr>
                    <th>Situação</th>
                    <td>
                        <span class="badge <?php echo $ativo ? 'badge-ativo' : 'badge-inativo'; ?>">
                            <?php echo $ativo ? 'Ativo' : 'Inativo'; ?>
                        </span>
                    </td>
                </tr>
                <?php if (!empty($f['criado_em'])): ?>
                <tr><th>Cadastrado em</th><td><?php echo date('d/m/Y H:i', strtotime($f['criado_em'])); ?></td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <div class="form-actions mt-16">
        <a href="cadastro.php?id=<?php echo $f['id']; ?>" class="btn btn-primary">Editar</a>
        <a href="listagem.php" class="btn btn-light">Voltar</a>
    </div>
</div>

</body>
</html>