<?php
require_once 'conexao.php';

$mensagem_erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'cadastrar') {
    $nome = trim($_POST['nome']);
    $link = trim($_POST['link']);
    $descricao = trim($_POST['descricao']);

    if (!empty($nome)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO jogos (nome, link, descricao) VALUES (?, ?, ?)");
            $stmt->execute([$nome, $link, $descricao]);
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $mensagem_erro = "O nome do jogo já existe na base de dados.";
            } else {
                $mensagem_erro = "Erro ao guardar: " . $e->getMessage();
            }
        }
    } else {
        $mensagem_erro = "O campo nome é obrigatório.";
    }
}

$stmt = $pdo->query("SELECT * FROM jogos ORDER BY codigo ASC");
$jogos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Catálogo Jogos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #f5f5f5; }
        .container { background-color: #fff; padding: 20px; border: 1px solid #ccc; max-width: 500px; margin: 0 auto; }
        h2 { text-align: center; margin-top: 0; }
        .btn { background-color: #5b72ce; color: #fff; padding: 6px 14px; text-decoration: none; border-radius: 3px; border: none; font-weight: bold; cursor: pointer; }
        .form-box { display: none; background: #eef; padding: 15px; margin: 15px 0; border-radius: 4px; }
        .form-group { margin-bottom: 10px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 4px; }
        .form-group input, .form-group textarea { width: 95%; padding: 6px; }
        .jogo-box { margin-top: 15px; margin-bottom: 15px; }
        .jogo-titulo { font-weight: bold; }
        .jogo-desc { margin-left: 20px; color: #333; margin-top: 3px; }
        .acoes { margin-left: 20px; font-size: 13px; margin-top: 3px; }
        .erro { color: red; }
    </style>
</head>
<body>

<div class="container">
    <h2>Catálogo Jogos</h2>

    <?php if ($mensagem_erro): ?>
        <p class="erro"><?= htmlspecialchars($mensagem_erro) ?></p>
    <?php endif; ?>

    <button class="btn" onclick="toggleForm()">+ Novo</button>

    <div id="form-novo" class="form-box" style="<?= $mensagem_erro ? 'display:block;' : '' ?>">
        <form action="index.php" method="POST">
            <input type="hidden" name="acao" value="cadastrar">
            <div class="form-group">
                <label>Nome:</label>
                <input type="text" name="nome" required>
            </div>
            <div class="form-group">
                <label>Link:</label>
                <input type="url" name="link">
            </div>
            <div class="form-group">
                <label>Descrição:</label>
                <textarea name="descricao" rows="3"></textarea>
            </div>
            <button type="submit" class="btn">Guardar</button>
        </form>
    </div>

    <hr>

    <?php foreach ($jogos as $jogo): ?>
        <div class="jogo-box">
            <div class="jogo-titulo">
                <?= htmlspecialchars($jogo['codigo']) ?> - <?= htmlspecialchars($jogo['nome']) ?> -
                <a href="<?= htmlspecialchars($jogo['link']) ?>" target="_blank"><?= htmlspecialchars($jogo['link']) ?></a>
            </div>
            <div class="jogo-desc">
                <?= nl2br(htmlspecialchars($jogo['descricao'])) ?>
            </div>
            <div class="acoes">
                <a href="editar.php?codigo=<?= $jogo['codigo'] ?>">Alterar</a> |
                <a href="excluir.php?codigo=<?= $jogo['codigo'] ?>" onclick="return confirm('Tem a certeza que pretende eliminar?');">Excluir</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
function toggleForm() {
    var f = document.getElementById('form-novo');
    f.style.display = (f.style.display === 'block') ? 'none' : 'block';
}
</script>

</body>
</html>