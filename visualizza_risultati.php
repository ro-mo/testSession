<?php
session_start();
require 'functions.php';
checkLogin();

$conn = connectDB();
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$utente = $_SESSION['utente'];

// Recupera tutti i test creati dall'account docente
$sql = "SELECT id, titolo FROM test WHERE creatore = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $utente);
$stmt->execute();
$test_list = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if (isset($_GET['id'])) {
    $test_id = intval($_GET['id']);
    $sql = "SELECT DISTINCT u.id, u.nome, u.cognome
            FROM utente u
            JOIN risposta_utente ru ON u.id = ru.utente_id
            JOIN domanda d ON ru.domanda_id = d.id
            WHERE d.test_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $test_id);
    $stmt->execute();
    $studenti = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Risultati Test</title>
    <link rel="stylesheet" href="css/style_visualizza_risultati.css">
</head>
<body>
    <header>
        <h1>Risultati Test</h1>
        <nav>
            <a href="index.php">Torna alla Dashboard</a>
        </nav>
    </header>

    <?php if (count($test_list) > 0) { ?>
        <h2>Seleziona un Test</h2>
        <form method="get">
            <label for="id">Test:</label>
            <select name="id" id="id" required>
                <?php foreach ($test_list as $test) { ?>
                    <option value="<?php echo $test['id']; ?>"><?php echo htmlspecialchars($test['titolo']); ?></option>
                <?php } ?>
            </select>
            <input type="submit" value="Visualizza Risultati">
        </form>

        <?php if (isset($studenti)) { ?>
            <?php if (count($studenti) > 0) { ?>
                <h2>Studenti che hanno svolto il test</h2>
                <div class="card-container">
                    <?php foreach ($studenti as $studente) { ?>
                        <div class="card">
                            <a href="dettagli_risultati.php?test_id=<?php echo $test_id; ?>&utente_id=<?php echo $studente['id']; ?>">
                                <?php echo htmlspecialchars($studente['nome'] . ' ' . $studente['cognome']); ?>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <p>Nessun studente ha svolto questo test.</p>
            <?php } ?>
        <?php } ?>
    <?php } else { ?>
        <p>Non ci sono test creati.</p>
    <?php } ?>
</body>
</html>