<?php //PHP eh o back end

/*Lembre-se, back end eh o q faz o site ou coisa do tipo funcionar

front end eh a parte visual*/

session_start(); //Aqui inicia a sessao (todas as vezes q tem uma sessao iniciada. Tem q sempre fechar)
include 'conexao.php'; // Incluir a conexao do banco de dados com script

// Limitador de paginas
$limit = 3; // limite de 3 paginas
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $limit;

// Barra de pesquisa
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Filtrar as mensagens
if (!empty($search)) {
    $sql = "SELECT COUNT(*) AS total FROM posts WHERE mensagem LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_param = "%" . $search . "%";
    $stmt->bind_param("s", $search_param);
    $stmt->execute();
    $result_count = $stmt->get_result();
    $total_posts = $result_count->fetch_assoc()['total'];

    $sql = "SELECT id, data_postagem, mensagem, imagem FROM posts WHERE mensagem LIKE ? ORDER BY data_postagem DESC LIMIT ?, ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $search_param, $offset, $limit);
} else {
    // Total de postagens 
    $sql = "SELECT COUNT(*) AS total FROM posts";
    $result_count = $conn->query($sql);
    $total_posts = $result_count->fetch_assoc()['total'];

    $sql = "SELECT id, data_postagem, mensagem, imagem FROM posts ORDER BY data_postagem DESC LIMIT ?, ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $offset, $limit);
}

$stmt->execute();
$result = $stmt->get_result();

// Calcula o numero de paginas q existe
$total_pages = ceil($total_posts / $limit);
?>

<!DOCTYPE html> <!--- HTML, JAVASCRIPT (por mais q eu n tenha usado) E CSS sao front end--->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href='style.css'>
    <title>My Diary</title>
    
</head>
<body>
    <div class="container">
        <h1>My Diary</h1>

        <!-- Pesquisa -->
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search posts..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>

        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<div class='post'>";
                echo "<h2>" . date('F j, Y', strtotime($row['data_postagem'])) . "</h2>";
                echo "<p>" . htmlspecialchars($row['mensagem'], ENT_QUOTES, 'UTF-8') . "</p>";
                if ($row['imagem']) {
                    echo "<img src='" . htmlspecialchars($row['imagem'], ENT_QUOTES, 'UTF-8') . "' alt='Post image' style='width: 50%;'>";
                }
                echo "</div><hr>";
            }
        } else {
            echo "<p>No posts found.</p>";
        }

        // Paginas
        if ($total_pages > 1) {
            echo '<div class="pagination">';
            if ($current_page > 1) {
                echo '<a href="?page=' . ($current_page - 1) . '&search=' . urlencode($search) . '">Previous</a>';
            }

            for ($i = 1; $i <= $total_pages; $i++) {
                if ($i == $current_page) {
                    echo '<span>' . $i . '</span>';
                } else {
                    echo '<a href="?page=' . $i . '&search=' . urlencode($search) . '">' . $i . '</a>';
                }
            }

            if ($current_page < $total_pages) {
                echo '<a href="?page=' . ($current_page + 1) . '&search=' . urlencode($search) . '">Next</a>';
            }
            echo '</div>';
        }

        $conn->close();
        ?>

        <!-- Botao de enviar email -->
        <div>
            <a href="mailto:ftmailenterprise@gmail.com?subject=New Message&body=Hello,">
                <button>Let`s be friends?</button>
            </a>
        </div>

    </div>
</body>
</html>