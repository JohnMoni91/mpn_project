<?php
session_start();
include 'conexao.php';

// Gerar um novo nome de guest se não existir na sessão
if (!isset($_SESSION['guest_name'])) {
    // Contador de guests armazenado em uma variável de sessão
    if (!isset($_SESSION['guest_counter'])) {
        $_SESSION['guest_counter'] = 1; // Começar com guest1
    } else {
        $_SESSION['guest_counter']++; // Incrementar contador
    }
    // Definir o nome do guest
    $_SESSION['guest_name'] = "guest" . $_SESSION['guest_counter'];
}

// O restante do seu código permanece o mesmo

$limit = 3;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $limit;
$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql_count = !empty($search) 
    ? "SELECT COUNT(*) AS total FROM posts WHERE mensagem LIKE ?" 
    : "SELECT COUNT(*) AS total FROM posts";

$stmt_count = $conn->prepare($sql_count);
if (!empty($search)) {
    $search_param = "%" . $search . "%";
    $stmt_count->bind_param("s", $search_param);
}
$stmt_count->execute();
$total_posts = $stmt_count->get_result()->fetch_assoc()['total'];

$sql_posts = !empty($search) 
    ? "SELECT id_post, data_postagem, mensagem, imagem FROM posts WHERE mensagem LIKE ? ORDER BY data_postagem DESC LIMIT ?, ?"
    : "SELECT id_post, data_postagem, mensagem, imagem FROM posts ORDER BY data_postagem DESC LIMIT ?, ?";

$stmt_posts = $conn->prepare($sql_posts);
if (!empty($search)) {
    $stmt_posts->bind_param("sii", $search_param, $offset, $limit);
} else {
    $stmt_posts->bind_param("ii", $offset, $limit);
}

$stmt_posts->execute();
$result = $stmt_posts->get_result();
$total_pages = ceil($total_posts / $limit);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>My Diary</title>
</head>
<body>
    <div class="login-button">
        <a href="login.php">
            <button>Login</button>
        </a>
    </div>

    <div class="container">
        <h1>My Diary</h1>

        <!--<div class="user-info">
            <p>Welcome, <?= htmlspecialchars($_SESSION['guest_name']); ?></p>
        </div>-->

        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search posts..." value="<?= htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class='post'>
                    <h2><?= date('F j, Y', strtotime($row['data_postagem'])); ?></h2>
                    <p><?= htmlspecialchars($row['mensagem'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php if ($row['imagem']): ?>
                        <img src='<?= htmlspecialchars($row['imagem'], ENT_QUOTES, 'UTF-8'); ?>' alt='Post image' style='width: 50%;'>
                    <?php endif; ?>

                    <div class='comments'>
                        <?php
                        $post_id = $row['id_post']; 
                        $sql_comments = "SELECT comment_text, commenter_name FROM comments WHERE post_id = ?";
                        $stmt_comments = $conn->prepare($sql_comments);
                        $stmt_comments->bind_param("i", $post_id);
                        $stmt_comments->execute();
                        $result_comments = $stmt_comments->get_result();

                        if ($result_comments->num_rows > 0): 
                            while ($comment = $result_comments->fetch_assoc()): ?>
                                <div class='comment'>
                                    <strong><?= htmlspecialchars($comment['commenter_name'], ENT_QUOTES, 'UTF-8'); ?></strong>: 
                                    <p><?= htmlspecialchars($comment['comment_text'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                            <?php endwhile; 
                        else: ?>
                            <p>No comments yet.</p>
                        <?php endif; ?>
                    </div>

                    <form method='POST' action='add_comment.php' class='comment-form'>
                        <input type='hidden' name='post_id' value='<?= $post_id; ?>'>
                        <input type='hidden' name='commenter_name' value='<?= htmlspecialchars($_SESSION['guest_name']); ?>'>
                        <textarea name='comment_text' placeholder='Let`s talk?' required></textarea>
                        <button type='submit'>Comment</button>
                    </form>

                </div>
                <hr>
            <?php endwhile; ?>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($current_page > 1): ?>
                        <a href="?page=<?= $current_page - 1; ?>&search=<?= urlencode($search); ?>">Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == $current_page): ?>
                            <span><?= $i; ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $i; ?>&search=<?= urlencode($search); ?>"><?= $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($current_page < $total_pages): ?>
                        <a href="?page=<?= $current_page + 1; ?>&search=<?= urlencode($search); ?>">Next</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <p>No posts found.</p>
        <?php endif; ?>

        <div>
            <a href="mailto:ftmailenterprise@gmail.com?subject=New Message&body=Hello,">
                <button>Let's be friends?</button>
            </a>
        </div>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>