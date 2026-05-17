<?php

    include 'conexao.php';

    if (!isset($_POST['descricao'], $_POST['valor']) || empty($_POST['descricao']) || empty($_POST['valor'])) {
    header('Location: ../index.php');
    exit;
} 

    $descricao = $_POST['descricao'];
    $valor = $_POST['valor'];
    $data = date('Y-m-d'); 


    $sql = " INSERT INTO despesas (descricao, valor,data) 
             VALUES (:descricao, :valor, :data) ";

    $smt = $conexao->prepare($sql); 
    $smt->bindParam(':descricao', $descricao);
    $smt->bindParam(':valor', $valor);
    $smt->bindParam(':data', $data);
 

    $smt->execute();

   header('Location: ../index.php');

?>