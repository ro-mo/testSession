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

// Recupera le informazioni dell'utente
$sql = "SELECT nome, cognome, classe FROM utente WHERE login = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $utente);
$stmt->execute();
$result = $stmt->get_result();
$utente_info = $result->fetch_assoc();
$nome = $utente_info['nome'];
$cognome = $utente_info['cognome'];
$classe = $utente_info['classe'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style_index.css">
</head>
<body>
    <header>
        <h1>Benvenuto, <?php echo htmlspecialchars($nome . ' ' . $cognome); ?>!</h1>
        <nav>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <?php if ($ruolo === "docente") { ?>
        <h2>Gestione Test</h2>
        <div class="card-container">
            <div class="card">
                <a href="crea_test.php">Crea/Modifica Test</a>
            </div>
            <div class="card">
                <a href="visualizza_risultati.php">Visualizza Risultati</a>
            </div>
        </div>
    <?php } else { ?>
        <h2>Test Disponibili</h2>
        <div class="card-container">
            <?php
            $sql = "SELECT t.id, t.titolo, t.descrizione
                    FROM test t
                    WHERE t.classe = ? AND t.visibile = 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $classe);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='card'><a href='dettagli_test.php?id=" . $row['id'] . "'>" . htmlspecialchars($row['titolo']) . ": " . htmlspecialchars($row['descrizione']) . "</a></div>";
                }
            } else {
                echo "<div class='no-tests'>Nessun test disponibile al momento.</div>";
            }
            ?>
        </div>
    <?php } ?>
</body>
</html>