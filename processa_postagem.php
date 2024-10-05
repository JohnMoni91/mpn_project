<?php
session_start(); 
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || !isset($_SESSION['id_usuario'])) {
        die("Usuário não autenticado."); 
    }

    $id_usuario = $_SESSION['id_usuario'];
    $mensagem = $_POST['mensagem'];
    $imagem = null;

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $target_dir = "uploads/";

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); 
        }

        $target_file = $target_dir . basename($_FILES["imagem"]["name"]);
        
        if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $target_file)) {
            $imagem = $target_file; 
        } else {
            echo "Erro ao mover o arquivo de upload.";
            exit; 
        }
    }

    $sql = "INSERT INTO posts (id_usuario, mensagem, imagem) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo "Erro na preparação do statement: " . $conn->error;
        exit;
    }

    if ($id_usuario === null) {
        echo "ID do usuário não encontrado.";
        exit;
    }

    $stmt->bind_param("iss", $id_usuario, $mensagem, $imagem);

    if ($stmt->execute()) {
        header('Location: index.php');
        exit;
    } else {
        echo "Erro ao criar postagem: " . $stmt->error;
    }

    // Feche o statement e a conexão
    $stmt->close();
    $conn->close();
}
?>
