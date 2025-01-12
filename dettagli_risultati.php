<?php
session_start();
require 'functions.php';
checkLogin();

$conn = connectDB();
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$test_id = intval($_GET['test_id']);
$utente_id = intval($_GET['utente_id']);

// Recupera le risposte dello studente per il test specifico
$sql = "SELECT d.testo AS domanda, r.testo AS risposta, r.corretta, ru.testo_libero
        FROM risposta_utente ru
        JOIN domanda d ON ru.domanda_id = d.id
        LEFT JOIN risposta r ON ru.risposta_id = r.id
        WHERE d.test_id = ? AND ru.utente_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $test_id, $utente_id);
$stmt->execute();
$risultati = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dettagli Risultati</title>
    <link rel="stylesheet" href="css/style_dettagli_risultati.css">
</head>
<body>
    <header>
        <h1>Dettagli Risultati</h1>
        <nav>
            <a href="visualizza_risultati.php?id=<?php echo $test_id; ?>">Torna ai Risultati</a>
        </nav>
    </header>

    <table>
        <thead>
            <tr>
                <th>Domanda</th>
                <th>Risposta</th>
                <th>Corretta</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($risultati as $risultato) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($risultato['domanda']); ?></td>
                    <td><?php echo htmlspecialchars($risultato['risposta'] ?? $risultato['testo_libero']); ?></td>
                    <td><?php echo isset($risultato['corretta']) ? ($risultato['corretta'] ? 'Sì' : 'No') : ''; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>