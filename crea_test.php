<?php
session_start();
// Controllo se sono loggato
if (!isset($_SESSION["utente"])) {
    header("location: login.php");
    die();
}
// Se sono loggato mostro i dati degli utenti
$servername = "localhost";
$username = "root";
$password = "";
$database = "quiz";
$conn = new mysqli($servername, $username, $password, $database);

if(isset($_POST['crea_test'])) {
    if(!isset($_POST['nome_test']) || !isset($_POST['descrizione_test'])) {
        echo "Compila tutti i campi";
        die();
    }
    $nome_test = $_POST['nome_test'];
    $descrizione_test = $_POST['descrizione_test'];
    $sql = "INSERT INTO test (titolo, descrizione, creatore) VALUES ('$nome_test', '$descrizione_test', '".$_SESSION['utente']."')";
    $conn->query($sql);
    header("location: index.php");
    die();
}

if(isset($_POST['indietro'])) {
    header("location: index.php");
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
<body>
    <form action="crea_test.php" method="post">
        <br><input type="text" name="nome_test" placeholder="Nome test"><br>
        <input type="text" name="descrizione_test" placeholder="Descrizione test">
        <br>
        <input type="button" value="+ Aggiungi domanda" name="aggiungi_domanda">
        <script>
            document.getElementsByName("aggiungi_domanda")[0].addEventListener("click", function() {
                document.getElementById("domande").innerHTML += "<input type='text' name='domanda' placeholder='Domanda'><br><input type='text' name='risposta1' placeholder='Risposta 1'><input type='checkbox' name='risposta1' id='risposta1'><br><input type='text' name='risposta2' placeholder='Risposta 2'><input type='checkbox' name='risposta2' id='risposta2'><br><input type='text' name='risposta3' placeholder='Risposta 3'><input type='checkbox' name='risposta3' id='risposta3'><br><input type='text' name='risposta4' placeholder='Risposta 4'><input type='checkbox' name='risposta4' id='risposta4'>";
                document.getElementById("domande").innerHTML += "<br><br>";
            });
            document.getElementsByName("crea_test")[0].addEventListener("click", function() {
                if(document.getElementById("domande") == ""){
                    alert("Inserisci almeno una domanda");
                    return;
                }
            });
        </script>
        <div id="domande"></div>
        <br><br>
        <input type="submit" value="Crea test" name="crea_test">
        <input type="submit" value="Indietro" name="indietro">
    </form>
</body>
</html>