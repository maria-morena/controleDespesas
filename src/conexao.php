<?php

$host = getenv("PGHOST");
$db = getenv("PGDATABASE");
$user = getenv("PGUSER");
$senha = getenv("PGPASSWORD");
$port = getenv("PGPORT");

try {

    $conexao = new PDO(
        "pgsql:host=$host;port=$port;dbname=$db",
        $user,
        $senha
    );

    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  
    $conexao->exec("
        CREATE TABLE IF NOT EXISTS usuarios (
            id SERIAL PRIMARY KEY,
            email VARCHAR(100) NOT NULL,
            senha VARCHAR(255) NOT NULL
        )
    ");


    $conexao->exec("
        CREATE TABLE IF NOT EXISTS despesas (
            id SERIAL PRIMARY KEY,
            descricao VARCHAR(255) NOT NULL,
            valor DECIMAL(10,2) NOT NULL,
            data DATE
        )
    ");

 
    $verifica = $conexao->query("
        SELECT COUNT(*) FROM usuarios
    ");

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

    echo "Erro: " . $e->getMessage();
}

?>