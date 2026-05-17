<?php
$url = "https://economia.awesomeapi.com.br/json/last/USD-BRL";


$resposta = @file_get_contents($url);

if ($resposta !== false) {
    $dados = json_decode($resposta, true);
    $cotacao = $dados['USDBRL']['bid'] ?? '0.00';
} else {
    $cotacao = "Indisponível";
}

echo "Dólar: R$ " . $cotacao;
?>