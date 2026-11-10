<?php

    include 'conexao.php';
    require_once 'categorizar.php';

    if (!isset($_POST['descricao'], $_POST['valor']) || empty($_POST['descricao']) || empty($_POST['valor'])) {
    header('Location: ../index.php');
    exit;
}

    $descricao = $_POST['descricao'];
    $valor = $_POST['valor'];
    // Usa a data do formulário; se vier vazia ou inválida, usa a data de hoje
    $dataInformada = $_POST['data'] ?? '';
    $data = preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataInformada) ? $dataInformada : date('Y-m-d');

    // A IA sugere a categoria; se falhar, vem "Outros"
    $categoria = categorizarDespesa($descricao, (float) $valor);

    $sql = " INSERT INTO despesas (descricao, valor, data, categoria)
             VALUES (:descricao, :valor, :data, :categoria) ";

    $smt = $conexao->prepare($sql);
    $smt->bindParam(':descricao', $descricao);
    $smt->bindParam(':valor', $valor);
    $smt->bindParam(':data', $data);
    $smt->bindParam(':categoria', $categoria);

    $smt->execute();

   header('Location: ../index.php');

?>
