<?php
// index.php — Ponto de entrada
session_start();
if (!empty($_SESSION['usuario_logado'])) {
    header('Location: listagem.php');
} else {
    header('Location: login.php');
}
exit;