<?php
session_start();
// Controllo se sono loggato
if (!isset($_SESSION['utente'])) {
    header('Location: login.php');
    die();
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "quiz";
$conn = new mysqli($servername, $username, $password, $database);

//create test
if (isset($_POST['create_test'])) {
    header('Location: crea_test.php');
    die();
}

//logout
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: login.php');
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
    <p> Ciao <?php echo $_SESSION["utente"]; ?> </p>
    <form action="index.php" method="post">
        <?php
        /*if($_SESSION['utente'] == 'biscaro.fabio@maxplanck.edu.it') {
            echo "<input type='submit' value='Create' name='create_test'>";
        }*/
        ?>
        <input type='submit' value='Create' name='create_test'>
        <input type="submit" value="Logout" name="logout">
    </form>

    <!-- elenco quiz disponibili -->
    <h5>Quiz disponibili</h5>
    <?php
    $sql = "SELECT * FROM test";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Titolo</th><th>Descrizione</th><th>Creatore</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>".$row["id"]."</td>"."<td>".$row["titolo"]."</td>"."<td>".$row["descrizione"]."</td>".
                "<td>".$row["creatore"]."</td><td><a href='index.php?delete=" . $row["id"] . "'></a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No quiz found";
    }
   ?>
</body>
</html>