<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

include "src/conexao.php";
include "src/cotacao.php";

$sqlTotal = "SELECT SUM(valor) as total FROM despesas";
$resultTotal = $conexao->query($sqlTotal);
$total = $resultTotal->fetch(PDO::FETCH_OBJ);

$sql = "SELECT * FROM despesas";
$consulta = $conexao->query($sql);

$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id) {
    $sqlU = "SELECT * FROM despesas WHERE id = :id";
    $smt = $conexao->prepare($sqlU);
    $smt->bindParam(':id', $id);
    $smt->execute();
    $despesas = $smt->fetch(PDO::FETCH_OBJ);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Controle de Gastos</title>

<style>
body {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background-color: #f8fafc;
    color: #1e293b;
    display: flex;
    justify-content: center;
    padding: 30px;
}

.container {
    width: 100%;
    max-width: 900px;
}

.topo {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.usuario {
    font-size: 14px;
    color: #64748b;
}

.btn-sair {
    background-color: #64748b;
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 13px;
}

.btn-sair:hover {
    background-color: #475569;
}

h2 {
    margin-bottom: 10px;
}

form {
    background: white;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 25px;
    border: 1px solid #e2e8f0;
}

label {
    font-size: 14px;
    color: #64748b;
}

input {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    margin-bottom: 15px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
}

button {
    background-color: #2563eb;
    color: white;
    padding: 10px 18px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

button:hover {
    background-color: #1d4ed8;
}

.total-box {
    background: white;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 20px;
    border: 1px solid #e2e8f0;
    font-size: 18px;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 10px;
    overflow: hidden;
}

th {
    background-color: #2563eb;
    color: white;
    padding: 12px;
    text-align: left;
}

td {
    padding: 12px;
    border-top: 1px solid #e2e8f0;
}

tr:hover {
    background-color: #f1f5f9;
}

.acoes {
    display: flex;
    gap: 12px;
    align-items: center;
}

.btn-editar {
    color: #2563eb;
    text-decoration: none;
    font-weight: 500;
    font-size: 14px;
}

.btn-editar:hover {
    text-decoration: underline;
}

.acoes form {
    margin: 0;
}

.btn-excluir {
    background-color: #ef4444;
    color: white;
    border: none;
    padding: 6px 12px;
    font-size: 13px;
    border-radius: 6px;
    cursor: pointer;
}

.btn-excluir:hover {
    background-color: #dc2626;
}

.cotacao-box {
    background: white;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 20px;
    border: 1px solid #e2e8f0;
    font-size: 18px;
}
</style>
</head>

<body>

<div class="container">


<div class="topo">
    <div>
        <h2><?php echo isset($id) ? "Editar Gasto" : "Adicionar Gasto"; ?></h2>
        <div class="usuario">Logado como: <?php echo $_SESSION['usuario']; ?></div>
    </div>

    <a href="logout.php" class="btn-sair">Sair</a>
</div>

<form method="POST" action="<?php echo isset($id) ? 'src/editar.php' : 'src/inserir.php'; ?>">
    
    <?php if ($id): ?>
        <input type="hidden" name="id" value="<?php echo $despesas->id; ?>">
    <?php endif; ?>

    <label>Descrição</label>
    <input type="text" name="descricao" required
        value="<?php echo isset($despesas) ? $despesas->descricao : '' ?>">

    <label>Valor</label>
    <input type="number" step="0.01" name="valor" required
        value="<?php echo isset($despesas) ? $despesas->valor : '' ?>">

    <label>Data</label>
    <input type="date" name="data"
        value="<?php echo isset($despesas) ? date('Y-m-d', strtotime($despesas->data)) : '' ?>">

    <button type="submit">Salvar</button>
</form>

<div class="total-box">
    💰 Total de gastos: 
    R$ <?php echo number_format($total->total ?? 0, 2, ',', '.'); ?>
</div>

<div class="cotacao-box">
    💵 Dólar Hoje:
    R$ <?php echo number_format($dolar, 2, ',', '.'); ?>
</div>

<table>
<tr>
    <th>Descrição</th>
    <th>Valor</th>
    <th>Data</th>
    <th>Ações</th>
</tr>

<?php while ($linha = $consulta->fetch(PDO::FETCH_OBJ)) { ?>
<tr>
    <td><?php echo $linha->descricao ?></td>
    <td>R$ <?php echo number_format($linha->valor, 2, ',', '.'); ?></td>
    <td><?php echo date('d/m/Y', strtotime($linha->data)); ?></td>
    <td class="acoes">

        <a class="btn-editar" href="index.php?id=<?php echo $linha->id; ?>">
            Editar
        </a>

        <form action="src/excluir.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $linha->id; ?>">
            <button class="btn-excluir" type="submit"
                onclick="return confirm('Tem certeza que deseja excluir?')">
                Excluir
            </button>
        </form>

    </td>
</tr>
<?php } ?>

</table>

</div>

</body>
</html>