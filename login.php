<?php
// Abilito la sessione
session_start();
// verifica nel db se l'utente esiste
// Se c'è il post...
// print_r($_POST);
if (isset($_POST["comando"])) {
    // Apro la connessione
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "quiz";
    $conn = new mysqli($servername, $username, $password,$database);
    // Verifico se cè l'utente (query select)
    $post_login = htmlspecialchars($_POST["login"]); // tiveron.st.sebastiano@maxplanck.edu.it
    $post_password = htmlspecialchars($_POST["password"]); // password
    $sql = "select * from utente where login='$post_login' AND password='$post_password'";
    $result = $conn->query($sql);
    // print_r($result);
    // Se c'è l'utente salvo qualcosa in sessione
    if ($result->num_rows==1) {
        $_SESSION["utente"] = $post_login;
        header("location: index.php");
    } else {
        echo "Utente non trovato";
    }
    // echo "$sql";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
<body>
    <form method="post">
        Login: <input type="text" name="login"><br>
        Password: <input type="text" name="password"><br>
        <input type="submit" name="comando" value="Login"><br>
</form>
</body>
</html>