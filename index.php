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
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
    <style>
        .no-tests {
            padding: 20px;
            margin: 20px 0;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
            text-align: center;
            font-size: 1.2em;
            color: #333;
        }
        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            flex: 1 1 calc(33.333% - 20px);
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            text-align: center;
            background-color: #f9f9f9;
            transition: background-color 0.3s;
        }
        .card:hover {
            background-color: #e9e9e9;
        }
        .card a {
            text-decoration: none;
            color: #333;
            font-size: 1.2em;
        }
        .card a:hover {
            color: #007bff;
        }
    </style>
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
                    LEFT JOIN svolgimento_test st ON t.id = st.test_id AND st.utente_id = ?
                    WHERE st.utente_id IS NULL AND t.classe = ? AND t.visibile = 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $utente, $classe);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='card'><a href='svolgi_test.php?id=" . $row['id'] . "'>" . htmlspecialchars($row['titolo']) . ": " . htmlspecialchars($row['descrizione']) . "</a></div>";
                }
            } else {
                echo "<div class='no-tests'>Nessun test disponibile al momento.</div>";
            }
            ?>
        </div>
    <?php } ?>
</body>
</html>
