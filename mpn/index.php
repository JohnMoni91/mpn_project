<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "diario";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}


$sql = "SELECT data_postagem, mensagem, imagem FROM posts ORDER BY data_postagem DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
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

        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<div class='post'>";
                echo "<h2>" . date('F j, Y', strtotime($row['data_postagem'])) . "</h2>";
                echo "<p>" . htmlspecialchars($row['mensagem'], ENT_QUOTES, 'UTF-8') . "</p>";
                if ($row['imagem']) {
                    echo "<img src='" . htmlspecialchars($row['imagem'], ENT_QUOTES, 'UTF-8') . "' alt='Imagem da postagem'>";
                }
                echo "</div><hr>";
            }
        } else {
            echo "<p>Nenhuma postagem encontrada.</p>";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
