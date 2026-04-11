<?php
$host = "localhost";
$user = "postgres";
$porta = "5432"; 
$password = "senha";
$db = "controle_gastos";

$conexao = new PDO(
    "pgsql:host=$host;port=$porta;dbname=$db",
    $user,
    $password
);
?>