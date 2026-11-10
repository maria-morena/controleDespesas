<?php
error_reporting(0);
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

include "src/conexao.php";
require_once "src/resumo.php";

$periodo = intervaloDoMes($_GET['mes'] ?? null);

// Despesas do mês escolhido
$sql = "SELECT valor, categoria FROM despesas WHERE data >= :inicio AND data < :fim";
$stmt = $conexao->prepare($sql);
$stmt->bindValue(':inicio', $periodo['inicio']);
$stmt->bindValue(':fim', $periodo['fim']);
$stmt->execute();
$resumo = calcularResumo($stmt->fetchAll(PDO::FETCH_ASSOC));

// Total do mês anterior, para comparar
$sqlAnterior = "SELECT COALESCE(SUM(valor), 0) FROM despesas WHERE data >= :inicio AND data < :fim";
$stmt = $conexao->prepare($sqlAnterior);
$stmt->bindValue(':inicio', $periodo['inicioAnterior']);
$stmt->bindValue(':fim', $periodo['inicio']);
$stmt->execute();
$totalAnterior = (float) $stmt->fetchColumn();

// A IA só é chamada quando a pessoa clica no botão (economiza a cota da API)
$pediuIa = isset($_GET['ia']);
$textoIa = $pediuIa ? gerarTextoResumo($resumo, $totalAnterior, $periodo['nome']) : null;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Resumo do mês</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f8fafc; color: #1e293b; display: flex; justify-content: center; padding: 30px; }
        .container { width: 100%; max-width: 900px; }
        .topo { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .voltar { color: #2563eb; text-decoration: none; font-size: 14px; }
        .caixa { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #e2e8f0; }
        form { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        input[type=month] { padding: 8px; border-radius: 6px; border: 1px solid #e2e8f0; }
        button { background-color: #2563eb; color: white; padding: 9px 16px; border: none; border-radius: 6px; cursor: pointer; }
        button:hover { background-color: #1d4ed8; }
        .numeros { display: flex; gap: 30px; flex-wrap: wrap; font-size: 15px; color: #64748b; }
        .numeros strong { display: block; font-size: 22px; color: #1e293b; }
        .linha { display: grid; grid-template-columns: 160px 1fr 110px; gap: 12px; align-items: center; margin: 10px 0; font-size: 14px; }
        .barra { background: #eff6ff; border-radius: 999px; height: 12px; }
        .barra span { display: block; background: #2563eb; height: 12px; border-radius: 999px; }
        .ia { border-left: 4px solid #2563eb; line-height: 1.6; }
        .aviso { color: #64748b; font-size: 13px; margin-top: 10px; }
        .erro { color: #b45309; }
    </style>
</head>
<body>
<div class="container">
    <div class="topo">
        <h2>📊 Resumo de <?= htmlspecialchars($periodo['nome']) ?></h2>
        <a class="voltar" href="index.php">← Voltar às despesas</a>
    </div>

    <div class="caixa">
        <form method="GET">
            <label for="mes">Mês:</label>
            <input type="month" id="mes" name="mes" value="<?= htmlspecialchars($periodo['mes']) ?>">
            <button type="submit">Ver números</button>
            <button type="submit" name="ia" value="1">🤖 Gerar resumo com IA</button>
        </form>
    </div>

    <div class="caixa numeros">
        <div><strong><?= reais($resumo['total']) ?></strong>total do mês</div>
        <div><strong><?= $resumo['quantidade'] ?></strong>despesas</div>
        <div><strong><?= reais($totalAnterior) ?></strong>mês anterior</div>
    </div>

    <div class="caixa">
        <h3>Por categoria</h3>
        <?php if ($resumo['quantidade'] === 0): ?>
            <p>Nenhuma despesa registrada neste mês.</p>
        <?php endif; ?>
        <?php foreach ($resumo['porCategoria'] as $categoria => $valor):
            $percentual = $resumo['total'] > 0 ? round($valor / $resumo['total'] * 100) : 0; ?>
            <div class="linha">
                <span><?= htmlspecialchars($categoria) ?></span>
                <div class="barra"><span style="width: <?= $percentual ?>%"></span></div>
                <span><?= reais($valor) ?> (<?= $percentual ?>%)</span>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($pediuIa): ?>
        <div class="caixa ia">
            <h3>🤖 Resumo com IA</h3>
            <?php if ($textoIa !== null): ?>
                <p><?= nl2br(htmlspecialchars($textoIa)) ?></p>
                <p class="aviso">Texto gerado por IA a partir dos totais acima. Confira os números na tabela.</p>
            <?php elseif ($resumo['quantidade'] === 0): ?>
                <p>Não há despesas neste mês para resumir.</p>
            <?php else: ?>
                <p class="erro">Não foi possível gerar o resumo agora. Os números acima continuam corretos; tente de novo em alguns segundos.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
