<?php
$host = getenv('DB_HOST') ?: "localhost";
$port = getenv('DB_PORT') ?: "5432";

$db   = getenv('DB_NAME') ?: "controle_gastos_khxu"; 
$user = getenv('DB_USER') ?: "postgres";
$pass = getenv('DB_PASS') ?: "senha";

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