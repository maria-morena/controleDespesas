<?php
$host = getenv('DB_HOST') ?: "dpg-d84jkj8g4nts73f8lmb0-a"; 
$port = getenv('DB_PORT') ?: "5432";
$db   = getenv('DB_NAME') ?: "controle_gastos_khxu"; 
$user = getenv('DB_USER') ?: "admin";
$pass = getenv('DB_PASS') ?: "4eGccqYJaUSiGJh8pFJQHNYe3wcH2rSp"; 

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