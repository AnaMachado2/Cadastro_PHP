<?php
// processar.php — Lida com ações de backend (excluir, etc.)
session_start();
if (empty($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit;
}

require_once 'conexao.php';

$acao  = isset($_GET['acao'])  ? $_GET['acao']        : '';
$id    = isset($_GET['id'])    ? (int)$_GET['id']     : 0;
$busca = isset($_GET['busca']) ? $_GET['busca']        : '';

switch ($acao) {

    case 'excluir':
        if ($id > 0) {
            $pdo  = getConexao();
            $stmt = $pdo->prepare('DELETE FROM funcionarios WHERE id = :id');
            $stmt->execute(array(':id' => $id));
            $_SESSION['flash'] = 'Funcionário excluído com sucesso.';
        }
        $redir = 'listagem.php';
        if ($busca !== '') $redir .= '?busca=' . urlencode($busca);
        header('Location: ' . $redir);
        exit;

    default:
        header('Location: listagem.php');
        exit;
}