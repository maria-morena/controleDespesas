<?php

function validarValor($valor) {
    return $valor > 0;
}

function validarDescricao($descricao) {
    return !empty(trim($descricao));
}

echo "Rodando testes...\n";

#Teste 1 - caminho feliz
if (validarValor(100) === true) {
    echo "Teste 1 OK\n";
} else {
    echo "Teste 1 ERRO\n";
}

#Teste 2 - valor inválido 
if (validarValor(-50) === false) {
    echo "Teste 2 OK\n";
} else {
    echo "Teste 2 ERRO\n";
}

#Teste 3 - caso limite 
if (validarValor(0) === false) {
    echo "Teste 3 OK\n";
} else {
    echo "Teste 3 ERRO\n";
}

#Teste 4 - descrição vazia
if (validarDescricao("") === false) {
    echo "Teste 4 OK\n";
} else {
    echo "Teste 4 ERRO\n";
}

#Teste 5 - valor muito alto
if (validarValor(999999999) === true) {
    echo "Teste 5 OK\n";
} else {
    echo "Teste 5 ERRO\n";
}

echo "Fim dos testes.\n";

?>