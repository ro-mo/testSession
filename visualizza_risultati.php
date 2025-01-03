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
    $utente_id = isset($_GET['utente_id']) ? intval($_GET['utente_id']) : null;
    $risultati = getRisultatiTest($conn, $test_id, $utente_id);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Risultati Test</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
<body>
    <header>
        <h1>Risultati Test</h1>
        <nav>
            <a href="index.php">Torna alla Dashboard</a>
        </nav>
    </header>

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

    <?php if (isset($risultati)) { ?>
        <h2>Risultati</h2>
        <table>
            <thead>
                <tr>
                    <th>Domanda</th>
                    <th>Risposta</th>
                    <th>Corretta</th>
                    <th>Utente</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $risultati->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['domanda']); ?></td>
                        <td><?php echo htmlspecialchars($row['risposta'] ?? $row['testo_libero']); ?></td>
                        <td><?php echo isset($row['corretta']) ? ($row['corretta'] ? 'Sì' : 'No') : ''; ?></td>
                        <td><?php echo htmlspecialchars($row['nome'] . ' ' . $row['cognome']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</body>
</html>