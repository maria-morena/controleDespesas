<?php

$host = getenv("PGHOST");
$db = getenv("PGDATABASE");
$user = getenv("PGUSER");
$senha = getenv("PGPASSWORD");
$port = getenv("PGPORT");

$conexao = new PDO(
    "pgsql:host=$host;port=$port;dbname=$db",
    $user,
    $senha
);

?>