<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "diario";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data_postagem = $_POST['data_postagem'];
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
        }
    }

    $sql = "INSERT INTO posts (data_postagem, mensagem, imagem) VALUES ('$data_postagem', '$mensagem', '$imagem')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Postagem criada com sucesso!";
    } else {
        echo "Erro ao criar postagem: " . $conn->error;
    }

    $conn->close();
}
?>