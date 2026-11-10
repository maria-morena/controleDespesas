<?php
// Categorização automática de despesas com IA (Google Gemini).
// Se a IA falhar ou responder algo inesperado, a categoria é "Outros".

require_once __DIR__ . '/gemini.php';

const CATEGORIAS = [
    'Alimentação',
    'Transporte',
    'Moradia',
    'Saúde',
    'Educação',
    'Lazer',
    'Compras',
    'Contas e serviços',
    'Outros',
];

function montarPrompt(string $descricao, float $valor): string
{
    $lista = implode(', ', CATEGORIAS);
    $valorFormatado = number_format($valor, 2, ',', '.');

    return "Você classifica despesas pessoais de uma pessoa no Brasil.\n"
        . "Escolha UMA categoria desta lista: $lista.\n"
        . "Se não tiver certeza, use \"Outros\".\n"
        . "Responda apenas com JSON no formato {\"categoria\": \"...\"}.\n\n"
        . "Descrição da despesa: $descricao\n"
        . "Valor: R$ $valorFormatado";
}

// Pede ao Gemini uma resposta em JSON que só pode conter uma das categorias.
function chamarGemini(string $prompt): ?string
{
    $configuracao = [
        'temperature' => 0,
        'responseMimeType' => 'application/json',
        'responseSchema' => [
            'type' => 'OBJECT',
            'properties' => [
                'categoria' => ['type' => 'STRING', 'enum' => CATEGORIAS],
            ],
            'required' => ['categoria'],
        ],
    ];

    return chamarGeminiApi($prompt, $configuracao, 8);
}

// $chamarIa permite trocar a chamada real por uma resposta simulada nos testes.
function categorizarDespesa(string $descricao, float $valor, ?callable $chamarIa = null): string
{
    if (trim($descricao) === '') {
        return 'Outros';
    }

    $chamarIa = $chamarIa ?? 'chamarGemini';

    try {
        $texto = $chamarIa(montarPrompt($descricao, $valor));
    } catch (Throwable $e) {
        return 'Outros';
    }

    if (!is_string($texto)) {
        return 'Outros';
    }

    $json = json_decode($texto, true);
    $categoria = is_array($json) ? ($json['categoria'] ?? null) : null;

    return in_array($categoria, CATEGORIAS, true) ? $categoria : 'Outros';
}
