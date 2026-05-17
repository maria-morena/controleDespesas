<?php
include "conexao.php";

if (
    isset($_POST['id']) &&
    isset($_POST['descricao']) &&
    isset($_POST['valor'])
) {

    $id = (int) $_POST['id'];
    $descricao = $_POST['descricao'];
    $valor = $_POST['valor'];
    $data = !empty($_POST['data']) ? $_POST['data'] : null;

    $sql = "UPDATE despesas 
            SET descricao = :descricao, 
                valor = :valor, 
                data = :data 
            WHERE id = :id";

    $stmt = $conexao->prepare($sql);

    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':valor', $valor);
    $stmt->bindParam(':data', $data);
    $stmt->bindParam(':id', $id);

    $stmt->execute();
}

// O erro estava na linha abaixo (faltava o ponto e vírgula)
header("Location: ../index.php"); 
exit;
?>