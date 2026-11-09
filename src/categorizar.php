<?php
// Categorização automática de despesas com IA (Google Gemini).
// Se a IA falhar ou responder algo inesperado, a categoria é "Outros".

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

// Chama a API do Gemini e devolve o texto da resposta, ou null se algo falhar.
function chamarGemini(string $prompt): ?string
{
    $chave = getenv('GEMINI_API_KEY');
    if (!$chave) {
        return null;
    }

    $modelo = getenv('GEMINI_MODEL') ?: 'gemini-3.6-flash';
    $url = "https://generativelanguage.googleapis.com/v1beta/models/$modelo:generateContent";

    $corpo = [
        'contents' => [['parts' => [['text' => $prompt]]]],
        'generationConfig' => [
            'temperature' => 0,
            'responseMimeType' => 'application/json',
            'responseSchema' => [
                'type' => 'OBJECT',
                'properties' => [
                    'categoria' => ['type' => 'STRING', 'enum' => CATEGORIAS],
                ],
                'required' => ['categoria'],
            ],
        ],
    ];

    $contexto = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\nx-goog-api-key: $chave\r\n",
            'content' => json_encode($corpo, JSON_UNESCAPED_UNICODE),
            'timeout' => 8,
            'ignore_errors' => true,
        ],
    ]);

    $resposta = @file_get_contents($url, false, $contexto);
    if ($resposta === false) {
        return null;
    }

    $dados = json_decode($resposta, true);
    return $dados['candidates'][0]['content']['parts'][0]['text'] ?? null;
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
