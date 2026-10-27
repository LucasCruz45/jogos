<?php
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['codigo'])) {
    $codigo = (int)$_POST['codigo'];

    try {
        $stmt = $pdo->prepare("DELETE FROM jogos WHERE codigo = ?");
        $stmt->execute([$codigo]);
    } catch (PDOException $e) {
        // Tratar erro se o registro não puder ser apagado (ex: chave estrangeira)
    }
}

header("Location: index.php");
exit;
?>