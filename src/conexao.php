<?php

$host = getenv('MYSQLHOST');
$db = getenv('MYSQLDATABASE');
$user = getenv('MYSQLUSER');
$senha = getenv('MYSQLPASSWORD');
$port = getenv('MYSQLPORT');

try {

    $conexao = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8",
        $user,
        $senha
    );

    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexao->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    die("Erro de conexão com o banco: " . $e->getMessage());
}