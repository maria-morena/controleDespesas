<?php
include 'conexao.php';

if (isset($_POST['id']) && !empty($_POST['id'])) {
    // verifica se o ID foi enviado
    $id = $_POST['id'];
} else {
    header('Location: ../index.php');
    exit;
}

// garante que o id seja inteiro
$id = (int) $_POST['id'];

$sql = "DELETE FROM despesas WHERE id = :id";

$stmt = $conexao->prepare($sql);
$stmt->bindParam(':id', $id);

$stmt->execute();

// redireciona de volta
header('Location: ../index.php');
exit;
?>