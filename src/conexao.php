<?php

$host = $_ENV['MYSQLHOST'];
$db = $_ENV['MYSQLDATABASE'];
$user = $_ENV['MYSQLUSER'];
$senha = $_ENV['MYSQLPASSWORD'];
$port = $_ENV['MYSQLPORT'];

try {

    $conexao = new PDO(
        "mysql:host=$host;port=$port;dbname=$db",
        $user,
        $senha
    );

    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // tabela usuarios
    $conexao->exec("
        CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(100) NOT NULL,
            senha VARCHAR(255) NOT NULL
        )
    ");

    // tabela despesas
    $conexao->exec("
        CREATE TABLE IF NOT EXISTS despesas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            descricao VARCHAR(255) NOT NULL,
            valor DECIMAL(10,2) NOT NULL,
            data DATE
        )
    ");

    // usuário admin
    $verifica = $conexao->query("SELECT COUNT(*) FROM usuarios");

    if ($verifica->fetchColumn() == 0) {

        $conexao->exec("
            INSERT INTO usuarios (email, senha)
            VALUES (
                'admin@email.com',
                md5('123456')
            )
        ");
    }

} catch(PDOException $e) {

    die("Erro: " . $e->getMessage());
}

?>