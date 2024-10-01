<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    // Redireciona para a página de login se não estiver logado
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postar no Diário</title>
</head>
<body>
  <h1>Nova Postagem</h1>
  <form action="processa_postagem.php" method="POST" enctype="multipart/form-data">
    <label for="data_postagem">Data:</label>
    <input type="date" id="data_postagem" name="data_postagem" required><br><br>

    <label for="mensagem">Mensagem:</label><br>
    <textarea id="mensagem" name="mensagem" rows="5" cols="30" required></textarea><br><br>

    <label for="imagem">Imagem (opcional):</label>
    <input type="file" id="imagem" name="imagem" accept="image/*"><br><br>

    <input type="submit" value="Postar">
  </form>
  
  <a href="logout.php">Sair</a>
</body>
</html>