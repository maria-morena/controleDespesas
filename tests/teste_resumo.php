<?php
// Testes do resumo do mês com respostas SIMULADAS da IA.
// Não chamam a API nem o banco: rodam rápido, de graça e sem chave.

require __DIR__ . '/../src/resumo.php';

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

echo "Rodando testes do resumo do mês...\n";

$linhas = [
    ['valor' => '35.90', 'categoria' => 'Alimentação'],
    ['valor' => '1200.00', 'categoria' => 'Moradia'],
    ['valor' => '64.10', 'categoria' => 'Alimentação'],
    ['valor' => '22.50', 'categoria' => null],
];

// 1. Cálculo dos números (feito pelo PHP, não pela IA)
$resumo = calcularResumo($linhas);
verificar('soma o total do mês', $resumo['total'] === 1322.5);
verificar('conta as despesas', $resumo['quantidade'] === 4);
verificar('agrupa por categoria', $resumo['porCategoria']['Alimentação'] === 100.0);
verificar('categoria vazia vira Outros', $resumo['porCategoria']['Outros'] === 22.5);
verificar('maior categoria vem primeiro', array_key_first($resumo['porCategoria']) === 'Moradia');
verificar('guarda a maior despesa', $resumo['maior']['valor'] === 1200.0);

$vazio = calcularResumo([]);
verificar('mês vazio tem total zero', $vazio['total'] === 0.0 && $vazio['quantidade'] === 0);

// 2. Intervalo de datas do mês
$nov = intervaloDoMes('2026-11');
verificar('início e fim de novembro', $nov['inicio'] === '2026-11-01' && $nov['fim'] === '2026-12-01');
verificar('mês anterior de novembro', $nov['inicioAnterior'] === '2026-10-01');
verificar('nome do mês em português', $nov['nome'] === 'novembro de 2026');

$dez = intervaloDoMes('2026-12');
verificar('dezembro termina em janeiro do ano seguinte', $dez['fim'] === '2027-01-01');

$jan = intervaloDoMes('2027-01');
verificar('mês anterior de janeiro é dezembro', $jan['inicioAnterior'] === '2026-12-01');

$invalido = intervaloDoMes("2026-13' OR 1=1", '2026-11-10');
verificar('texto inválido usa o mês atual', $invalido['mes'] === '2026-11');

// 3. O prompt leva só os totais
$prompt = montarPromptResumo($resumo, 900.0, 'novembro de 2026');
verificar('prompt contém o total', str_contains($prompt, 'R$ 1.322,50'));
verificar('prompt contém a categoria e o percentual', str_contains($prompt, 'Moradia: R$ 1.200,00 (91%)'));
verificar('prompt contém o mês anterior', str_contains($prompt, 'R$ 900,00'));
verificar('prompt proíbe inventar números', str_contains($prompt, 'Não invente valores'));

// 4. Texto da IA (respostas simuladas)
$texto = gerarTextoResumo($resumo, 900.0, 'novembro', fn($p) => 'Você gastou mais com Moradia.');
verificar('texto válido é devolvido', $texto === 'Você gastou mais com Moradia.');

$texto = gerarTextoResumo($resumo, 900.0, 'novembro', fn($p) => '**Moradia** foi o maior gasto.');
verificar('asteriscos de markdown são removidos', $texto === 'Moradia foi o maior gasto.');

$texto = gerarTextoResumo($resumo, 900.0, 'novembro', fn($p) => null);
verificar('falha da API devolve null', $texto === null);

$texto = gerarTextoResumo($resumo, 900.0, 'novembro', fn($p) => '   ');
verificar('resposta vazia devolve null', $texto === null);

$texto = gerarTextoResumo($resumo, 900.0, 'novembro', function ($p) {
    throw new RuntimeException('timeout');
});
verificar('erro na chamada devolve null', $texto === null);

$texto = gerarTextoResumo($resumo, 900.0, 'novembro', fn($p) => str_repeat('a', 2000));
verificar('texto muito longo é cortado', mb_strlen($texto) === TAMANHO_MAXIMO_RESUMO + 1);

$chamou = false;
$texto = gerarTextoResumo($vazio, 0.0, 'novembro', function ($p) use (&$chamou) {
    $chamou = true;
    return 'texto';
});
verificar('mês vazio não chama a IA', $texto === null && $chamou === false);

echo $falhas === 0 ? "Todos os testes passaram.\n" : "$falhas teste(s) falharam.\n";
exit($falhas === 0 ? 0 : 1);
