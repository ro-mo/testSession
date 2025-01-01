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
// inizializzo i valori
$id=$nome=$cognome=$login=$password="";


// Cancellazione dati
if (isset($_GET["delete"])) {
    $id = $conn->real_escape_string($_GET["delete"]);
    if ($id>1) {
        $sql = "delete from utente where id=$id";
        $conn -> query($sql);
    }
    $id = "";
}

// Modifica dei dati
if (isset($_GET["update"])) {
    $id = $_GET["update"];
    // Carico i dati dll'utente cliccato
    $sql = "select * from utente where id=".$id;
    $result = $conn->query($sql);
    // Inserisco i valori letti dal database nelle variabili
    if ($result->num_rows==1) {
        $row = $result->fetch_assoc();
        $nome = $row["nome"];
        $cognome = $row["cognome"];
        $login = $row["login"];
        $password = $row["password"];        
    }
    // echo "$nome $cognome $login $password"; // debug
}

// Aggiunta o modifica utente
if (isset($_POST["save"])) {
    // echo "salvataggio";
    $id = $conn->real_escape_string($_POST["id"]);
    $nome=$conn->real_escape_string($_POST["nome"]); //...
    $cognome=$conn->real_escape_string($_POST["cognome"]); //...
    $login=$conn->real_escape_string($_POST["login"]); //...
    $password=$conn->real_escape_string($_POST["password"]); //...
    // Distinguo l'update dall'inserimento
    if ($id=="") {
        $sql = "insert into utente (nome, cognome, login, password) values ('$nome',
            '$cognome','$login','$password')";
    } else {
        $sql = "update utente set nome='$nome', password='$password', 
            cognome='$cognome', login='$login' where id=$id ";
    }
    $conn -> query($sql); // Aggiungo o modifico i dati
    //echo "$sql";
    // Svuoto i campi per permettere una nuova operazione
    $id=$nome=$cognome=$login=$password="";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amministrazione</title>
    <!-- Minified version -->
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
<body>
    <p> Ciao <?php echo $_SESSION["utente"]; ?> </p>
    <h5>Nuovo Utente/Modifica</h5>

    <form method="post" action="gestione_utenti.php">
        Login: <input type="text" name="login" value="<?php echo $login?>"><br>
        Password: <input type="text" name="password" value="<?php echo $password?>"><br>
        Nome: <input type="text" name="nome" value="<?php echo $nome?>"><br>
        Cognome: <input type="text" name="cognome" value="<?php echo $cognome?>"><br>
        <input type="hidden" name="id" value="<?php echo $id?>">
        <input type="submit" name="save">
    </form>

    <h1>Lista utenti</h1>
    <!-- Mostro la tabella con i dati -->
    <?php
        $result = $conn->query("select * from utente"); 
        echo "<table border='1'>";
        echo "<tr><th>Id</th><th>Nome</th><th>Cognome</th><th>Login</th>
            <th></th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td><a href='gestione_utenti.php?update=".$row["id"]."'>".$row["id"]."</a></td>"."<td>".$row["nome"].
                "</td>"."<td>".$row["cognome"]."</td>"."<td>".$row["login"]."</td>".
                "<td><a href='gestione_utenti.php?delete=".$row["id"]."'>X</a></td>" ;
            echo "</tr>";
        }
        echo "</table>";
    ?>
</body>
</html>