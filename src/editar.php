<?php
include "conexao.php";
require_once "categorizar.php";

if (
    isset($_POST['id']) &&
    isset($_POST['descricao']) &&
    isset($_POST['valor'])
) {

    $id = (int) $_POST['id'];
    $descricao = $_POST['descricao'];
    $valor = $_POST['valor'];
    $data = !empty($_POST['data']) ? $_POST['data'] : null;

    // A descrição pode ter mudado, então a IA categoriza de novo
    $categoria = categorizarDespesa($descricao, (float) $valor);

    $sql = "UPDATE despesas 
            SET descricao = :descricao, 
                valor = :valor, 
                data = :data,
                categoria = :categoria
            WHERE id = :id";

    $stmt = $conexao->prepare($sql);

    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':valor', $valor);
    $stmt->bindParam(':data', $data);
    $stmt->bindParam(':categoria', $categoria);
    $stmt->bindParam(':id', $id);

    $stmt->execute();
}

header("Location: ../index.php"); 
exit;
?>