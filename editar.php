<?php
require_once 'conexao.php';

$codigo = isset($_GET['codigo']) ? (int)$_GET['codigo'] : null;

if (!$codigo) {
    header("Location: index.php");
    exit;
}

// Busca os dados atuais do banco de dados
$stmt = $pdo->prepare("SELECT * FROM jogos WHERE codigo = ?");
$stmt->execute([$codigo]);
$jogo = $stmt->fetch();

if (!$jogo) {
    header("Location: index.php");
    exit;
}

$mensagem_erro = '';

// Variáveis para preencher o formulário
$nome = $jogo['nome'];
$link = $jogo['link'];
$descricao = $jogo['descricao'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $link = trim($_POST['link'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    if (!empty($nome)) {
        try {
            $stmt = $pdo->prepare("UPDATE jogos SET nome = ?, link = ?, descricao = ? WHERE codigo = ?");
            $stmt->execute([$nome, $link, $descricao, $codigo]);
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $mensagem_erro = "O nome do jogo já existe na base de dados.";
            } else {
                $mensagem_erro = "Erro ao atualizar: " . $e->getMessage();
            }
        }
    } else {
        $mensagem_erro = "O campo nome é obrigatório.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Jogo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #f5f5f5; }
        .container { background-color: #fff; padding: 20px; border: 1px solid #ccc; max-width: 500px; margin: 0 auto; }
        .form-group { margin-bottom: 12px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea { width: 95%; padding: 8px; }
        .btn { background-color: #5b72ce; color: #fff; padding: 8px 15px; text-decoration: none; border-radius: 3px; border: none; cursor: pointer; }
        .erro { color: red; }
    </style>
</head>
<body>

<div class="container">
    <h2>Editar Jogo #<?= htmlspecialchars($jogo['codigo']) ?></h2>

    <?php if ($mensagem_erro): ?>
        <p class="erro"><?= htmlspecialchars($mensagem_erro) ?></p>
    <?php endif; ?>

    <form action="editar.php?codigo=<?= $codigo ?>" method="POST">
        <div class="form-group">
            <label>Nome:</label>
            <input type="text" name="nome" value="<?= htmlspecialchars($nome) ?>" required>
        </div>
        <div class="form-group">
            <label>Link:</label>
            <input type="url" name="link" value="<?= htmlspecialchars($link) ?>">
        </div>
        <div class="form-group">
            <label>Descrição:</label>
            <textarea name="descricao" rows="4"><?= htmlspecialchars($descricao) ?></textarea>
        </div>
        <button type="submit" class="btn">Guardar Alterações</button>
        <a href="index.php" style="margin-left: 10px;">Cancelar</a>
    </form>
</div>

</body>
</html>