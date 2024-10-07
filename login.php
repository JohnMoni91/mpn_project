<?php
session_start();
include 'conexao.php';

// Inicializar a contagem de convidados, se não estiver definida
if (!isset($_SESSION['guest_count'])) {
    $_SESSION['guest_count'] = 0; // Contagem de convidados começa em 0
}

// Verifica se o usuário já está logado como admin
if (isset($_SESSION['logado']) && $_SESSION['logado'] === true && $_SESSION['role'] === 'admin') {
    header('Location: postDiary.php'); // Redireciona para a página de administração
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Se o botão "Enter as Guest" for clicado
    if (isset($_POST['guest'])) {
        $_SESSION['guest_count']++; // Incrementa a contagem de convidados
        $_SESSION['logado'] = true;
        $_SESSION['usuario'] = "guest" . $_SESSION['guest_count']; // Define o nome do usuário como guest1, guest2, etc.
        $_SESSION['role'] = "guest"; 
        header('Location: index.php'); 
        exit();
    }

    $usuario = $_POST['usuario'] ?? null;
    $senha = $_POST['senha'] ?? null;

    if ($usuario && $senha) {
        // Consulta para buscar o usuário no banco de dados
        $query = "SELECT * FROM usuarios WHERE usuario = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario_dados = $resultado->fetch_assoc();

        if ($usuario_dados) {
            // Verifica se a senha corresponde ao hash armazenado no banco de dados
            if (password_verify($senha, $usuario_dados['senha'])) {
                // Login bem-sucedido, define a sessão
                $_SESSION['logado'] = true;
                $_SESSION['usuario'] = $usuario_dados['usuario'];
                $_SESSION['role'] = $usuario_dados['role'];

                // Redireciona com base no papel do usuário
                if ($_SESSION['role'] === 'admin') {
                    header('Location: postDiary.php'); // Admin redirecionado para a página de administração
                } else {
                    header('Location: index.php'); // Usuário comum redirecionado para a página inicial
                }
                exit();
            } else {
                // Senha incorreta
                $erro = "Error.";
            }
        } else {
            // Usuário não encontrado
            $erro = "Error.";
        }
    } else {
        // Campos não preenchidos
        $erro = "Error.";
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