<?php
session_start();
include "src/conexao.php";

if ($_POST) {
    $email = $_POST['email'];
    $senha = md5($_POST['senha']);

    $sql = "SELECT * FROM usuarios WHERE email = :email AND senha = :senha";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':senha', $senha);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $_SESSION['usuario'] = $email;
        header("Location: index.php");
        exit;
    } else {
        $erro = "Email ou senha inválidos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Login</title>

<style>
body {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0;
}


.login-box {
    background: white;
    padding: 30px;
    border-radius: 12px;
    width: 100%;
    max-width: 350px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    text-align: center;
}

h2 {
    margin-bottom: 20px;
    color: #1e293b;
}


input {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    font-size: 14px;
}

input:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
}


button {
    width: 100%;
    background-color: #2563eb;
    color: white;
    padding: 10px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 15px;
}

button:hover {
    background-color: #1d4ed8;
}

.erro {
    background: #fee2e2;
    color: #b91c1c;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-size: 14px;
}
</style>

</head>

<body>

<div class="login-box">

    <h2>Login</h2>

    <?php if (isset($erro)) { ?>
        <div class="erro"><?php echo $erro; ?></div>
    <?php } ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Digite seu email" required>
        <input type="password" name="senha" placeholder="Digite sua senha" required>
        <button type="submit">Entrar</button>
    </form>

</div>

</body>
</html>