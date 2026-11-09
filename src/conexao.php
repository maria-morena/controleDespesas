<?php
// Os dados de acesso vêm SEMPRE de variáveis de ambiente.
// Nunca coloque senhas no código: o repositório é público.
$host = getenv('DB_HOST');
$port = getenv('DB_PORT') ?: "5432";
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');

try {
    $conexao = new PDO(
        "pgsql:host=$host;port=$port;dbname=$db",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>