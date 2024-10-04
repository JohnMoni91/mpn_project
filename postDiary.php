<?php
session_start();

// Verifica se o usuário está logado e se é o admin_username
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || $_SESSION['usuario'] !== 'admin_username') {
    // Log para depuração
    error_log("Error. Session: " . print_r($_SESSION, true));
    
    // Redireciona para a página de login se não estiver logado ou não for o admin_username
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css"> 
    <title>Postar no Diário</title>
</head>
<body>
    <div class="post-container">
        <h1>Nova Postagem</h1>
        <form action="processa_postagem.php" method="POST" enctype="multipart/form-data">
            <label for="data_postagem">Data:</label>
            <input type="date" id="data_postagem" name="data_postagem" required>

            <label for="mensagem">Mensagem:</label>
            <textarea id="mensagem" name="mensagem" rows="5" required></textarea>

            <label for="imagem">Imagem (opcional):</label>
            <input type="file" id="imagem" name="imagem" accept="image/*">

            <input type="submit" value="Postar">
        </form>
        
        <a href="logout.php" class="logout-btn">Sair</a>
    </div>
</body>
</html>