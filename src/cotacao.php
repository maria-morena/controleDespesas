<?php
$url = "https://economia.awesomeapi.com.br/json/last/USD-BRL";

$options = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: PHP\r\n"
    ]
];
$context = stream_context_create($options);
$resposta = @file_get_contents($url, false, $context);


$cotacao_formatada = "0,00"; 

if ($resposta !== false) {
    $dados = json_decode($resposta, true);
    if (isset($dados['USDBRL']['bid'])) {
        $cotacao = $dados['USDBRL']['bid'];
        $cotacao_formatada = number_format($cotacao, 2, ',', '.');
    } else {
        $cotacao_formatada = "Erro";
    }
} else {
    $cotacao_formatada = "Indisponível";
}
?>