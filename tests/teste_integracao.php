<?php

$url = "https://economia.awesomeapi.com.br/json/last/USD-BRL";

$resposta = file_get_contents($url);

if ($resposta === false) {
    die("Erro ao conectar com API");
}

$dados = json_decode($resposta, true);

if (!isset($dados['USDBRL']['bid'])) {
    die("Cotação não encontrada");
}

echo "Teste de integração OK";