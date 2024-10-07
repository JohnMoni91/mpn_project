<?php
session_start();
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'];
    $comment_text = $_POST['comment_text'];
    $commenter_name = $_SESSION['usuario'] ?? 'Anônimo'; // Captura o nome do usuário logado

    // Verificar se o post existe
    $sql_check_post = "SELECT COUNT(*) FROM posts WHERE id_post = ?";
    $stmt_check_post = $conn->prepare($sql_check_post);
    $stmt_check_post->bind_param("i", $post_id);
    $stmt_check_post->execute();
    $stmt_check_post->bind_result($count);
    $stmt_check_post->fetch();

    if ($count == 0) {
        echo "Erro: O post_id não existe.";
        exit;
    }

    // Fechar o statement do SELECT antes de continuar
    $stmt_check_post->close();

    // Inserir o comentário com o nome do comentador
    $sql_insert = "INSERT INTO comments (post_id, comment_text, commenter_name) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("iss", $post_id, $comment_text, $commenter_name);

    if ($stmt_insert->execute()) {
        header("Location: index.php");
        exit;
    } else {
        echo "Erro ao adicionar comentário: " . $stmt_insert->error;
    }

    // Fechar o statement de inserção
    $stmt_insert->close();
}

// Fechar a conexão
$conn->close();
?>