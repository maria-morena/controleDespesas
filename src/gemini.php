<?php
// Chamada genérica à API do Google Gemini, usada pela categorização e pelo resumo do mês.
// A chave vem da variável de ambiente GEMINI_API_KEY e nunca fica no código.

// Envia o prompt e devolve o texto da resposta, ou null se algo falhar.
function chamarGeminiApi(string $prompt, array $configuracao, int $timeout = 8): ?string
{
    $chave = getenv('GEMINI_API_KEY');
    if (!$chave) {
        registrarFalha('GEMINI_API_KEY não configurada');
        return null;
    }

    $modelo = getenv('GEMINI_MODEL') ?: 'gemini-3.6-flash';
    $url = "https://generativelanguage.googleapis.com/v1beta/models/$modelo:generateContent";

    $corpo = [
        'contents' => [['parts' => [['text' => $prompt]]]],
        'generationConfig' => $configuracao,
    ];

    $contexto = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\nx-goog-api-key: $chave\r\n",
            'content' => json_encode($corpo, JSON_UNESCAPED_UNICODE),
            'timeout' => $timeout,
            'ignore_errors' => true,
        ],
    ]);

    // Até 2 tentativas: a segunda só acontece em falhas passageiras
    // (excesso de pedidos ou instabilidade do servidor do Gemini).
    for ($tentativa = 1; $tentativa <= 2; $tentativa++) {
        $resposta = @file_get_contents($url, false, $contexto);

        if ($resposta === false) {
            registrarFalha("sem resposta (timeout ou rede), tentativa $tentativa");
            return null; // não repete: a pessoa já esperou o timeout inteiro
        }

        $status = codigoHttp($http_response_header ?? []);
        $dados = json_decode($resposta, true);

        if ($status === 200) {
            $texto = $dados['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if ($texto === null) {
                registrarFalha('resposta 200 sem texto');
            }
            return $texto;
        }

        $mensagem = $dados['error']['message'] ?? 'sem mensagem';
        registrarFalha("HTTP $status, tentativa $tentativa: $mensagem");

        $passageira = in_array($status, [429, 500, 503], true);
        if (!$passageira || $tentativa === 2) {
            return null;
        }
        sleep(2);
    }

    return null;
}

// Lê o código HTTP (200, 429...) da primeira linha dos cabeçalhos da resposta.
function codigoHttp(array $cabecalhos): int
{
    if (isset($cabecalhos[0]) && preg_match('/\s(\d{3})\s?/', $cabecalhos[0], $m)) {
        return (int) $m[1];
    }
    return 0;
}

// Escreve o motivo nos Logs do Render. Nunca registra a chave nem dados das despesas.
function registrarFalha(string $motivo): void
{
    error_log("[gemini] Falha na IA: $motivo");
}
