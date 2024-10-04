<?php
session_start();
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['post_id'];
    $comment_text = $_POST['comment_text'];

    // Verifique se o post_id existe na tabela postagens
    $sql_check_post = "SELECT COUNT(*) FROM postagens WHERE id = ?";
    $stmt_check_post = $conn->prepare($sql_check_post);
    $stmt_check_post->bind_param("i", $post_id);
    $stmt_check_post->execute();
    $stmt_check_post->bind_result($count);
    $stmt_check_post->fetch();

    if ($count == 0) {
        echo "Erro: O post_id não existe.";
        exit;
    }

    // Inserindo o comentário
    $sql_insert = "INSERT INTO comments (post_id, comment_text, comment_date) VALUES (?, ?, NOW())";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("is", $post_id, $comment_text);

    if ($stmt_insert->execute()) {
        header("Location: index.php?page=" . $_POST['page'] . "&search=" . urlencode($_POST['search']));
        exit;
    } else {
        echo "Erro ao adicionar comentário.";
    }
}
?>
