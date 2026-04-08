<?php
// cadastro.php
session_start();
if (empty($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit;
}

require_once 'conexao.php';

$pagina_atual = 'listagem';
$sucesso = '';
$erro    = '';

// Modo edição?
$id_editar   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$funcionario = array(
    'id'       => 0,
    'nome'     => '',
    'cargo'    => '',
    'email'    => '',
    'telefone' => '',
    'ativo'    => true,
);

if ($id_editar > 0) {
    $pdo  = getConexao();
    $stmt = $pdo->prepare('SELECT * FROM funcionarios WHERE id = :id LIMIT 1');
    $stmt->execute(array(':id' => $id_editar));
    $row = $stmt->fetch();
    if ($row) {
        $funcionario = $row;
    } else {
        header('Location: listagem.php');
        exit;
    }
}

// Processar POST (salvar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_post   = isset($_POST['id'])       ? (int)$_POST['id']           : 0;
    $nome      = isset($_POST['nome'])     ? trim($_POST['nome'])         : '';
    $cargo     = isset($_POST['cargo'])    ? trim($_POST['cargo'])        : '';
    $email     = isset($_POST['email'])    ? trim($_POST['email'])        : '';
    $telefone  = isset($_POST['telefone']) ? trim($_POST['telefone'])     : '';
    $ativo_val = isset($_POST['ativo'])    ? ($_POST['ativo'] === '1')    : true;

    // Validação básica
    if ($nome === '' || $cargo === '' || $email === '') {
        $erro = 'Preencha os campos obrigatórios: Nome, Cargo e E-mail.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Informe um e-mail válido.';
    } else {
        $pdo = getConexao();
        try {
            if ($id_post > 0) {
                // UPDATE
                $stmt = $pdo->prepare(
                    'UPDATE funcionarios SET nome=:nome, cargo=:cargo, email=:email,
                     telefone=:telefone, ativo=:ativo WHERE id=:id'
                );
                $stmt->execute(array(
                    ':nome'     => $nome,
                    ':cargo'    => $cargo,
                    ':email'    => $email,
                    ':telefone' => $telefone,
                    ':ativo'    => $ativo_val ? 't' : 'f',
                    ':id'       => $id_post,
                ));
                $sucesso = 'Funcionário atualizado com sucesso!';
            } else {
                // INSERT
                $stmt = $pdo->prepare(
                    'INSERT INTO funcionarios (nome, cargo, email, telefone, ativo)
                     VALUES (:nome, :cargo, :email, :telefone, :ativo)'
                );
                $stmt->execute(array(
                    ':nome'     => $nome,
                    ':cargo'    => $cargo,
                    ':email'    => $email,
                    ':telefone' => $telefone,
                    ':ativo'    => $ativo_val ? 't' : 'f',
                ));
                $sucesso = 'Funcionário cadastrado com sucesso!';
            }
            // Repopular campos após sucesso em modo inserção
            if ($id_post === 0 && $sucesso) {
                $funcionario = array('id'=>0,'nome'=>'','cargo'=>'','email'=>'','telefone'=>'','ativo'=>true);
            } else {
                $funcionario['nome']     = $nome;
                $funcionario['cargo']    = $cargo;
                $funcionario['email']    = $email;
                $funcionario['telefone'] = $telefone;
                $funcionario['ativo']    = $ativo_val;
            }
        } catch (PDOException $e) {
            $erro = 'Erro ao salvar: ' . $e->getMessage();
        }
    }
}

$cargos = array('Administrador', 'Gerente', 'Analista', 'Assistente', 'Desenvolvedor', 'Designer', 'Suporte');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Funcionário</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="page-wrapper">
    <h2 class="page-title">Cadastro de Funcionários</h2>

    <?php if ($sucesso): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($sucesso); ?></div>
    <?php endif; ?>
    <?php if ($erro): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <!-- User icon -->
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
            </svg>
            Cadastro de Funcionários
        </div>

        <div class="card-body">
            <form method="POST" action="cadastro.php<?php echo $funcionario['id'] ? '?id=' . $funcionario['id'] : ''; ?>" id="formCadastro">
                <input type="hidden" name="id" value="<?php echo (int)$funcionario['id']; ?>">

                <div class="form-grid">
                    <!-- ID (somente leitura) -->
                    <div class="form-group">
                        <label>ID</label>
                        <input type="text" value="<?php echo $funcionario['id'] ? $funcionario['id'] : 'Automático'; ?>" readonly
                               style="background:#f4f6fb;color:#999;">
                    </div>

                    <!-- Nome -->
                    <div class="form-group">
                        <label>Nome <span style="color:#dc3545">*</span></label>
                        <input type="text" name="nome" placeholder="Nome"
                               value="<?php echo htmlspecialchars($funcionario['nome']); ?>">
                    </div>

                    <!-- Cargo -->
                    <div class="form-group">
                        <label>Cargo <span style="color:#dc3545">*</span></label>
                        <div class="select-wrapper">
                            <select name="cargo">
                                <option value="">Cargo</option>
                                <?php foreach ($cargos as $c): ?>
                                    <option value="<?php echo $c; ?>"
                                        <?php echo ($funcionario['cargo'] === $c) ? 'selected' : ''; ?>>
                                        <?php echo $c; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- E-mail -->
                    <div class="form-group">
                        <label>E-mail <span style="color:#dc3545">*</span></label>
                        <input type="email" name="email" placeholder="E-mail"
                               value="<?php echo htmlspecialchars($funcionario['email']); ?>">
                    </div>

                    <!-- E-mail (coluna direita decorativa igual à imagem) -->
                    <div class="form-group">
                        <label>E-mail (confirmação)</label>
                        <input type="text" value="<?php echo htmlspecialchars($funcionario['email']); ?>"
                               placeholder="joao@empresa.com" readonly style="background:#f4f6fb;">
                    </div>

                    <!-- Telefone -->
                    <div class="form-group">
                        <label>Telefone</label>
                        <input type="tel" name="telefone" placeholder="Telefone"
                               value="<?php echo htmlspecialchars($funcionario['telefone']); ?>">
                    </div>

                    <!-- Situação -->
                    <div class="form-group">
                        <label>Situação</label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="ativo" value="1"
                                    <?php echo (!isset($funcionario['ativo']) || $funcionario['ativo'] == true || $funcionario['ativo'] === 't') ? 'checked' : ''; ?>>
                                Ativo
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="ativo" value="0"
                                    <?php echo (isset($funcionario['ativo']) && ($funcionario['ativo'] == false || $funcionario['ativo'] === 'f')) ? 'checked' : ''; ?>>
                                Inativo
                            </label>
                        </div>
                    </div>
                </div><!-- /form-grid -->

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <!-- Save icon -->
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="white"><path d="M17 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V7l-4-4zm-5 16a3 3 0 110-6 3 3 0 010 6zm3-10H5V5h10v4z"/></svg>
                        Salvar
                    </button>
                    <button type="reset" class="btn btn-light">Limpar</button>
                    <a href="listagem.php" class="btn btn-light">Voltar</a>
                    <a href="listagem.php" class="btn btn-secondary">Fechar</a>
                </div>

            </form>
        </div><!-- /card-body -->
    </div><!-- /card -->
</div>

</body>
</html>