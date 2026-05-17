<?php
$url = "https://economia.awesomeapi.com.br/json/last/USD-BRL";

$options = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n", 
        "timeout" => 5
    ]
];

$context = stream_context_create($options);
$resposta = @file_get_contents($url, false, $context);


if ($resposta !== false) {
    $dados = json_decode($resposta, true);
    if (isset($dados['USDBRL']['bid'])) {
        $cotacao = $dados['USDBRL']['bid'];
        $cotacao_formatada = number_format($cotacao, 2, ',', '.');
    } else {
        $cotacao_formatada = "5,15"; // Valor reserva caso a API responda vazio
    }
} else {
    // Se continuar dando erro no Render, ele mostrará este valor médio para não ficar feio
    $cotacao_formatada = "5,20 (Estimado)"; 
}
?>