<?php
session_start();

// Connessione al database
require 'functions.php';
$conn = connectDB();

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Verifica login
if (isset($_POST["comando"])) {
    $post_login = htmlspecialchars($_POST["login"]);
    $post_password = $_POST["password"];
    
    $sql = "SELECT * FROM utente WHERE login=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $post_login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $utente = $result->fetch_assoc();
        if (password_verify($post_password, $utente["password"])) {
            $_SESSION["utente"] = $utente["login"];
            $_SESSION["tipo"] = $utente["tipo"];
            header("Location: index.php");
            exit();
        } else {
            $errore = "Login o password errati.";
        }
    } else {
        $errore = "Login o password errati.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
<body>
    <h1>Login</h1>
    <?php if (isset($errore)) echo "<p style='color:red;'>$errore</p>"; ?>
    <form method="post">
        <label for="login">Login:</label>
        <input type="text" name="login" id="login" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br>

        <input type="submit" name="comando" value="Login">
    </form>
    <p>Non hai un account? <a href="register.php">Registrati</a></p>
</body>
</html>
