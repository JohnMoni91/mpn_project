<?php
$servername = "localhost"; // Ou o endereço do seu servidor
$username = "root";        // Usuário do banco de dados
$password = "";            // Senha do banco de dados
$dbname = "diario";     // Nome do banco de dados

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>
