<?php
session_start();
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['guest'])) {
        $_SESSION['logado'] = true;
        $_SESSION['usuario'] = "guest";
        $_SESSION['role'] = "guest"; 
        header('Location: index.php'); 
        exit();
    }

    $usuario = $_POST['usuario'] ?? null;
    $senha = $_POST['senha'] ?? null;

    if ($usuario && $senha) {
        $query = "SELECT * FROM usuarios WHERE usuario = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario_dados = $resultado->fetch_assoc();

        if ($usuario_dados) {
            if (password_verify($senha, $usuario_dados['senha'])) {
                // Successful login
                $_SESSION['logado'] = true;
                $_SESSION['usuario'] = $usuario_dados['usuario'];
                $_SESSION['role'] = $usuario_dados['role'];

                if ($_SESSION['role'] === 'admin') {
                    header('Location: postDiary.php');
                } else {
                    header('Location: index.php');
                }
                exit();
            } else {
                $erro = "Incorrect password.";
            }
        } else {
            $erro = "User not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href='st.css'>
    <title>Login</title>
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <?php if (isset($erro)) echo "<p class='error'>$erro</p>"; ?>
        
        <form method="POST" action="">
            <label for="usuario">Username:</label>
            <input type="text" id="usuario" name="usuario" required><br><br>

            <label for="senha">Password:</label>
            <input type="password" id="senha" name="senha" required><br><br>
            
            <input type="submit" value="Login">
        </form>

        <button class="register-btn" onclick="window.location.href='register.php';">Create Account</button>
        <form method="POST" action="">
            <input type="hidden" name="guest" value="true">
            <button type="submit" class="guest-btn">Enter as Guest</button>
        </form>

        <button onclick="window.location.href='index.php';">Back</button>
    </div>
</body>
</html>
