<?php
// Resumo do mês: o PHP calcula os números e a IA só escreve o texto a partir deles.
// A IA recebe apenas totais por categoria, nunca as descrições das despesas.

require_once __DIR__ . '/gemini.php';

const MESES = [
    1 => 'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho',
    'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro',
];

const TAMANHO_MAXIMO_RESUMO = 900;

// Recebe "2026-11" e devolve as datas do mês e do mês anterior.
// Se o texto vier inválido, usa o mês de $hoje.
function intervaloDoMes(?string $mes, ?string $hoje = null): array
{
    if (!is_string($mes) || !preg_match('/^(\d{4})-(0[1-9]|1[0-2])$/', $mes)) {
        $mes = date('Y-m', $hoje ? strtotime($hoje) : time());
    }

    $inicio = new DateTimeImmutable("$mes-01");

    return [
        'mes' => $mes,
        'inicio' => $inicio->format('Y-m-d'),
        'fim' => $inicio->modify('+1 month')->format('Y-m-d'),               // primeiro dia do mês seguinte
        'inicioAnterior' => $inicio->modify('-1 month')->format('Y-m-d'),
        'nome' => MESES[(int) $inicio->format('n')] . ' de ' . $inicio->format('Y'),
    ];
}

// Soma as despesas do mês. Cada linha precisa ter 'valor' e 'categoria'.
function calcularResumo(array $linhas): array
{
    $porCategoria = [];
    $total = 0.0;
    $maior = null;

    foreach ($linhas as $linha) {
        $valor = (float) $linha['valor'];
        $categoria = $linha['categoria'] ?: 'Outros';

        $total += $valor;
        $porCategoria[$categoria] = ($porCategoria[$categoria] ?? 0) + $valor;

        if ($maior === null || $valor > $maior['valor']) {
            $maior = ['valor' => $valor, 'categoria' => $categoria];
        }
    }

    arsort($porCategoria); // maior gasto primeiro

    return [
        'total' => round($total, 2),
        'quantidade' => count($linhas),
        'porCategoria' => array_map(fn($v) => round($v, 2), $porCategoria),
        'maior' => $maior,
    ];
}

function reais(float $valor): string
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function montarPromptResumo(array $resumo, float $totalAnterior, string $nomeMes): string
{
    $linhas = [];
    foreach ($resumo['porCategoria'] as $categoria => $valor) {
        $percentual = $resumo['total'] > 0 ? round($valor / $resumo['total'] * 100) : 0;
        $linhas[] = "- $categoria: " . reais($valor) . " ($percentual%)";
    }

    $comparacao = $totalAnterior > 0
        ? 'Total do mês anterior: ' . reais($totalAnterior)
        : 'Mês anterior: sem despesas registradas';

    return "Você ajuda uma pessoa no Brasil a entender os próprios gastos.\n"
        . "Escreva um resumo de 3 a 4 frases curtas, em português, sobre os gastos de $nomeMes.\n"
        . "Regras:\n"
        . "- Use SOMENTE os números abaixo. Não invente valores, datas nem porcentagens.\n"
        . "- Diga onde a pessoa mais gastou e compare com o mês anterior, se houver.\n"
        . "- Termine com uma sugestão prática e gentil, sem julgamentos.\n"
        . "- Texto simples, sem markdown, sem asteriscos e sem listas.\n\n"
        . "Dados:\n"
        . "Total do mês: " . reais($resumo['total']) . " em {$resumo['quantidade']} despesas\n"
        . "$comparacao\n"
        . "Por categoria:\n" . implode("\n", $linhas);
}

// Devolve o texto da IA ou null (mês vazio, falha da API ou resposta vazia).
// $chamarIa permite usar uma resposta simulada nos testes.
function gerarTextoResumo(array $resumo, float $totalAnterior, string $nomeMes, ?callable $chamarIa = null): ?string
{
    if ($resumo['quantidade'] === 0) {
        return null; // nada para resumir: não gasta chamada
    }

    $chamarIa = $chamarIa ?? fn($prompt) => chamarGeminiApi($prompt, ['temperature' => 0.4], 15);

    try {
        $texto = $chamarIa(montarPromptResumo($resumo, $totalAnterior, $nomeMes));
    } catch (Throwable $e) {
        return null;
    }

    if (!is_string($texto)) {
        return null;
    }

    // Limpa formatação que a IA às vezes coloca mesmo pedindo texto simples
    $texto = trim(str_replace(['**', '*', '#'], '', $texto));
    if ($texto === '') {
        return null;
    }

    if (mb_strlen($texto) > TAMANHO_MAXIMO_RESUMO) {
        $texto = rtrim(mb_substr($texto, 0, TAMANHO_MAXIMO_RESUMO)) . '…';
    }

    return $texto;
}
