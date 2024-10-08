<?php
session_start();
include 'conexao.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'] ?? null;
    $password = $_POST['password'] ?? null;


    if ($username && $password) {

        $query = "SELECT * FROM usuarios WHERE usuario = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Username already exists."; 
        } else {
            $password_hash = password_hash($password, PASSWORD_BCRYPT); 

            $query = "INSERT INTO usuarios (usuario, senha, role) VALUES (?, ?, 'user')";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $username, $password_hash);


            if ($stmt->execute()) {
                $success = "User registered successfully!";
            } else {
                $error = "Error registering user: " . $stmt->error;
            }
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href='sts.css'>
    <title>Register</title>
</head>
<body>
    <div class="register-container">
        <h1>Register</h1>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>
            
            <input type="submit" value="Register">
        </form>

        <button onclick="window.location.href='login.php';">Back</button>
    </div>
</body>
</html>
