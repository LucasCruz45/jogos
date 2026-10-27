<?php
require_once 'conexao.php'; // Padronizado para conexao.php (PDO)

$codigo = null;
$nome = '';
$link = '';
$descricao = '';
$erro = '';

// Processamento do formulário (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = !empty($_POST['codigo']) ? (int)$_POST['codigo'] : null;
    $nome = trim($_POST['nome'] ?? '');
    $link = trim($_POST['link'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    if (empty($nome)) {
        $erro = "O campo Nome é obrigatório.";
    } else {
        try {
            if ($codigo) {
                // Atualizar
                $stmt = $pdo->prepare("UPDATE jogos SET nome = ?, link = ?, descricao = ? WHERE codigo = ?");
                $stmt->execute([$nome, $link, $descricao, $codigo]);
            } else {
                // Inserir
                $stmt = $pdo->prepare("INSERT INTO jogos (nome, link, descricao) VALUES (?, ?, ?)");
                $stmt->execute([$nome, $link, $descricao]);
            }

            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $erro = "Erro ao guardar no banco de dados: " . $e->getMessage();
        }
    }
} 
// Se for requisição GET e for edição, carrega os dados do banco
elseif (isset($_GET['codigo'])) {
    $codigo = (int)$_GET['codigo'];
    $stmt = $pdo->prepare("SELECT * FROM jogos WHERE codigo = ?");
    $stmt->execute([$codigo]);
    if ($jogo = $stmt->fetch()) {
        $nome = $jogo['nome'];
        $link = $jogo['link'];
        $descricao = $jogo['descricao'];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?= $codigo ? 'Editar' : 'Novo' ?> Jogo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #f4f4f9; }
        .container { max-width: 500px; margin: auto; background: white; padding: 20px; border: 1px solid #ccc; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], textarea { width: 100%; padding: 8px; box-sizing: border-box; }
        .btn-salvar { background-color: #4A73E8; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 4px; }
        .btn-cancelar { color: #333; text-decoration: none; margin-left: 10px; }
        .erro { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h2><?= $codigo ? 'Editar' : 'Novo' ?> Jogo</h2>

        <?php if ($erro): ?>
            <div class="erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form method="POST" action="form.php<?= $codigo ? '?codigo=' . $codigo : '' ?>">
            <input type="hidden" name="codigo" value="<?= htmlspecialchars((string)$codigo) ?>">

            <div class="form-group">
                <label for="nome">Nome do Jogo:</label>
                <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($nome) ?>" required>
            </div>

            <div class="form-group">
                <label for="link">Link:</label>
                <input type="text" id="link" name="link" value="<?= htmlspecialchars($link) ?>">
            </div>

            <div class="form-group">
                <label for="descricao">Descrição:</label>
                <textarea id="descricao" name="descricao" rows="5"><?= htmlspecialchars($descricao) ?></textarea>
            </div>

            <button type="submit" class="btn-salvar">Salvar</button>
            <a href="index.php" class="btn-cancelar">Cancelar</a>
        </form>
    </div>
</body>
</html>