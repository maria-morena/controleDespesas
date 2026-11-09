<?php
// Testes da categorização com respostas SIMULADAS da IA.
// Não chamam a API de verdade: rodam rápido, de graça e sem precisar da chave.

require __DIR__ . '/../src/categorizar.php';

$falhas = 0;

function verificar(string $nome, bool $condicao): void
{
    global $falhas;
    if ($condicao) {
        echo "OK    - $nome\n";
    } else {
        echo "FALHA - $nome\n";
        $falhas++;
    }
}

echo "Rodando testes da categorização...\n";

// 1. Caminho feliz: a IA responde uma categoria válida
$resultado = categorizarDespesa('Almoço no restaurante', 35.90, fn($p) => '{"categoria": "Alimentação"}');
verificar('categoria válida é aceita', $resultado === 'Alimentação');

// 2. A IA inventa uma categoria fora da lista
$resultado = categorizarDespesa('Uber para o trabalho', 22.50, fn($p) => '{"categoria": "Mobilidade"}');
verificar('categoria fora da lista vira Outros', $resultado === 'Outros');

// 3. A API falha (sem resposta)
$resultado = categorizarDespesa('Conta de luz', 180.00, fn($p) => null);
verificar('falha da API vira Outros', $resultado === 'Outros');

// 4. A IA responde texto que não é JSON
$resultado = categorizarDespesa('Cinema', 40.00, fn($p) => 'Lazer');
verificar('resposta que não é JSON vira Outros', $resultado === 'Outros');

// 5. A chamada lança um erro
$resultado = categorizarDespesa('Farmácia', 60.00, function ($p) {
    throw new RuntimeException('timeout');
});
verificar('erro na chamada vira Outros', $resultado === 'Outros');

// 6. Descrição vazia não chama a IA
$chamou = false;
$resultado = categorizarDespesa('   ', 10.00, function ($p) use (&$chamou) {
    $chamou = true;
    return '{"categoria": "Lazer"}';
});
verificar('descrição vazia vira Outros sem chamar a IA', $resultado === 'Outros' && $chamou === false);

// 7. O prompt leva a descrição e a lista de categorias
$prompt = montarPrompt('Mensalidade da faculdade', 950.00);
verificar('prompt contém a descrição', str_contains($prompt, 'Mensalidade da faculdade'));
verificar('prompt contém a lista de categorias', str_contains($prompt, 'Contas e serviços'));

// 8. Leitura do código HTTP, usado para decidir se tenta de novo
verificar('lê o código 429', codigoHttp(['HTTP/1.1 429 Too Many Requests']) === 429);
verificar('lê o código 200', codigoHttp(['HTTP/2 200']) === 200);
verificar('sem cabeçalho devolve 0', codigoHttp([]) === 0);

echo $falhas === 0 ? "Todos os testes passaram.\n" : "$falhas teste(s) falharam.\n";

// Código de saída diferente de 0 faz o GitHub Actions ficar vermelho
exit($falhas === 0 ? 0 : 1);
