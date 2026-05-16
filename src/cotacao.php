<?php

$url = "https://economia.awesomeapi.com.br/json/last/USD-BRL";

$resposta = file_get_contents($url);

$dados = json_decode($resposta, true);

$dolar = $dados['USDBRL']['bid'];

?>