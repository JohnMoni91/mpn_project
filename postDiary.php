<?php
session_start();
include 'conexao.php';

$mensagem = '';
$imagem = '';
$uploadError = '';
$data_postagem = ''; // Variável para armazenar a data de postagem

$id_usuario = null;
$sql_usuario = "SELECT id_usuario FROM usuarios WHERE role = 'admin'"; 
$result_usuario = $conn->query($sql_usuario);

if ($result_usuario === false) {
    echo "Error. " . $conn->error;
    exit;
}

if ($result_usuario->num_rows > 0) {
    $row_usuario = $result_usuario->fetch_assoc();
    $id_usuario = $row_usuario['id_usuario'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mensagem = $_POST['mensagem'];
    $data_postagem = $_POST['data_postagem']; // Captura a data do input

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $target_dir = "uploads/";

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); 
        }

        $target_file = $target_dir . basename($_FILES["imagem"]["name"]);

        if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $target_file)) {
            $imagem = $target_file; 
        } else {
            $uploadError = "Error: upload file.";
        }
    }

    // Ajustar a consulta para incluir a data de postagem
    $sql = "INSERT INTO posts (id_usuario, mensagem, imagem, data_postagem) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo "Error: preparation " . $conn->error;
        exit;
    }

    // Adicione a data de postagem no bind_param
    $stmt->bind_param("isss", $id_usuario, $mensagem, $imagem, $data_postagem);

    if ($stmt->execute()) {
        header('Location: index.php');
        exit;
    } else {
        echo "Error on new post " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css"> 
    <title>Post</title>
</head>
<body>
    <div class="post-container">
        <h1>New post</h1>
        
        <?php if ($uploadError): ?>
            <div class="error"><?php echo htmlspecialchars($uploadError); ?></div>
        <?php endif; ?>

        <form action="postDiary.php" method="POST" enctype="multipart/form-data">
            <label for="data_postagem">Date:</label>
            <input type="date" id="data_postagem" name="data_postagem" required> <!-- Campo para data apenas -->

            <label for="mensagem">Messages</label>
            <textarea id="mensagem" name="mensagem" rows="5" required><?php echo htmlspecialchars($mensagem); ?></textarea>

            <label for="imagem">Image:</label>
            <input type="file" id="imagem" name="imagem" accept="image/*">

            <input type="submit" value="Post">
        </form>
        
        <a href="logout.php" class="logout-btn">Exit</a>
    </div>
</body>
</html>