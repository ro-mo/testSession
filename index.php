<?php
session_start();

// Verifica sessione attiva
if (!isset($_SESSION["utente"])) {
    header("Location: login.php");
    exit();
}

// Connessione al database
$servername = "localhost";
$username = "root";
$password = "";
$database = "quiz";
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$ruolo = $_SESSION["tipo"];
$utente = $_SESSION["utente"];

// Recupera la classe dell'utente
$sql = "SELECT classe FROM utente WHERE login = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $utente);
$stmt->execute();
$result = $stmt->get_result();
$utente_info = $result->fetch_assoc();
$classe = $utente_info['classe'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
<body>
    <header>
        <h1>Benvenuto, <?php echo htmlspecialchars($utente); ?>!</h1>
        <nav>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <?php if ($ruolo === "docente") { ?>
        <h2>Gestione Test</h2>
        <ul>
            <li><a href="crea_test.php">Crea/Modifica Test</a></li>
            <li><a href="visualizza_risultati.php">Visualizza Risultati</a></li>
        </ul>
    <?php } else { ?>
        <h2>Test Disponibili</h2>
        <ul>
            <?php
            $sql = "SELECT t.id, t.titolo, t.descrizione
                    FROM test t
                    LEFT JOIN svolgimento_test st ON t.id = st.test_id AND st.utente_id = ?
                    WHERE st.utente_id IS NULL AND t.classe = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $utente, $classe);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                echo "<li><a href='svolgi_test.php?id=" . $row['id'] . "'>" . htmlspecialchars($row['titolo']) . ": " . htmlspecialchars($row['descrizione']) . "</a></li>";
            }
            ?>
        </ul>
    <?php } ?>
</body>
</html>
