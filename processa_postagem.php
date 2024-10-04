<?php
session_start(); // Inicie a sessão
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifique se o usuário está logado e se a variável de sessão id_usuario está definida
    if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || !isset($_SESSION['id_usuario'])) {
        die("Usuário não autenticado."); // Opcional: encerra o script se o usuário não estiver logado
    }

    $id_usuario = $_SESSION['id_usuario']; // Certifique-se de que o ID do usuário está na sessão
    $mensagem = $_POST['mensagem'];
    $imagem = null;

    // Verifique se há um arquivo de imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $target_dir = "uploads/";

        // Crie o diretório se não existir
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); 
        }

        // Define o caminho do arquivo de destino
        $target_file = $target_dir . basename($_FILES["imagem"]["name"]);
        // Move o arquivo para o diretório de uploads
        if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $target_file)) {
            $imagem = $target_file; 
        } else {
            echo "Erro ao mover o arquivo de upload.";
            exit; // Encerra o script em caso de erro
        }
    }

    // Use prepared statement
    $sql = "INSERT INTO posts (id_usuario, mensagem, imagem) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Verifique se a preparação do statement foi bem-sucedida
    if ($stmt === false) {
        echo "Erro na preparação do statement: " . $conn->error;
        exit;
    }

    // Certifique-se de que id_usuario não é null
    if ($id_usuario === null) {
        echo "ID do usuário não encontrado.";
        exit;
    }

    $stmt->bind_param("iss", $id_usuario, $mensagem, $imagem);

    // Execute o statement
    if ($stmt->execute()) {
        header('Location: index.php');
        exit; // Certifique-se de encerrar o script após o redirecionamento
    } else {
        echo "Erro ao criar postagem: " . $stmt->error;
    }

    // Feche o statement e a conexão
    $stmt->close();
    $conn->close();
}
?>