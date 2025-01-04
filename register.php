<?php
session_start();

// Connessione al database
require 'functions.php';
$conn = connectDB();

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Registrazione utente
if (isset($_POST["comando"])) {
    $post_login = htmlspecialchars($_POST["login"]);
    $post_password = $_POST["password"];
    $post_nome = htmlspecialchars($_POST["nome"]);
    $post_cognome = htmlspecialchars($_POST["cognome"]);
    $post_ruolo = htmlspecialchars($_POST["ruolo"]);
    $post_classe = htmlspecialchars($_POST["classe"]);
    
    $password_hash = password_hash($post_password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO utente (login, password, nome, cognome, tipo, classe) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $post_login, $password_hash, $post_nome, $post_cognome, $post_ruolo, $post_classe);
    
    if ($stmt->execute()) {
        $_SESSION["utente"] = $post_login;
        $_SESSION["tipo"] = $post_ruolo;
        header("Location: index.php");
        exit();
    } else {
        $errore = "Errore nella registrazione. Riprova.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
    <script>
        function toggleClasseField() {
            var ruolo = document.getElementById("ruolo").value;
            var classeField = document.getElementById("classeField");
            if (ruolo === "docente") {
                classeField.style.display = "none";
            } else {
                classeField.style.display = "block";
            }
        }
    </script>
</head>
<body>
    <h1>Registrazione</h1>
    <?php if (isset($errore)) echo "<p style='color:red;'>$errore</p>"; ?>
    <form method="post">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="nome" required><br>

        <label for="cognome">Cognome:</label>
        <input type="text" name="cognome" id="cognome" required><br>

        <label for="login">Login:</label>
        <input type="text" name="login" id="login" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br>

        <label for="ruolo">Ruolo:</label>
        <select name="ruolo" id="ruolo" onchange="toggleClasseField()" required>
            <option value="studente">Studente</option>
            <option value="docente">Docente</option>
        </select><br>

        <div id="classeField">
            <label for="classe">Classe:</label>
            <input type="text" name="classe" id="classe"><br>
        </div>

        <input type="submit" name="comando" value="Registrati">
    </form>
</body>
</html>