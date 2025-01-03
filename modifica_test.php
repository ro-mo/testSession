<?php
session_start();
require 'functions.php';
checkLogin();

$conn = connectDB();
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

if (!isset($_GET['id'])) {
    die("ID del test non specificato.");
}

$test_id = intval($_GET['id']);

// Funzionalità CRUD per le domande
if (isset($_POST['azione'])) {
    $azione = $_POST['azione'];
    if ($azione === 'aggiungi_domanda' && !empty($_POST['testo']) && !empty($_POST['tipo'])) {
        $testo = htmlspecialchars($_POST['testo']);
        $tipo = htmlspecialchars($_POST['tipo']);
        $risposte = [];
        if ($tipo === 'multipla') {
            for ($i = 1; $i <= 4; $i++) {
                if (!empty($_POST["risposta$i"])) {
                    $risposte[] = [
                        'testo' => htmlspecialchars($_POST["risposta$i"]),
                        'corretta' => isset($_POST["corretta$i"]) ? 1 : 0
                    ];
                }
            }
        }
        addDomanda($conn, $test_id, $testo, $tipo, $risposte);
    } elseif ($azione === 'elimina_domanda' && !empty($_POST['domanda_id'])) {
        $domanda_id = intval($_POST['domanda_id']);
        $sql = "DELETE FROM domanda WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $domanda_id);
        $stmt->execute();
    }
}

// Recupera tutte le domande del test
$sql = "SELECT * FROM domanda WHERE test_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $test_id);
$stmt->execute();
$domande = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Test</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
<body>
    <header>
        <h1>Modifica Test</h1>
        <nav>
            <a href="index.php">Torna alla Dashboard</a>
        </nav>
    </header>

    <h2>Aggiungi Domanda</h2>
    <form method="post">
        <input type="hidden" name="azione" value="aggiungi_domanda">
        <label for="testo">Testo:</label>
        <textarea id="testo" name="testo" required></textarea><br>

        <label for="tipo">Tipo:</label>
        <select id="tipo" name="tipo" required>
            <option value="multipla">Multipla</option>
            <option value="libera">Libera</option>
        </select><br>

        <div id="risposte">
            <label for="risposta1">Risposta 1:</label>
            <input type="text" id="risposta1" name="risposta1"><input type="checkbox" name="corretta1"><br>
            <label for="risposta2">Risposta 2:</label>
            <input type="text" id="risposta2" name="risposta2"><input type="checkbox" name="corretta2"><br>
            <label for="risposta3">Risposta 3:</label>
            <input type="text" id="risposta3" name="risposta3"><input type="checkbox" name="corretta3"><br>
            <label for="risposta4">Risposta 4:</label>
            <input type="text" id="risposta4" name="risposta4"><input type="checkbox" name="corretta4"><br>
        </div>

        <input type="submit" value="Aggiungi Domanda">
    </form>

    <h2>Lista Domande</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Testo</th>
                <th>Tipo</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($domande as $domanda) { ?>
                <tr>
                    <td><?php echo $domanda['id']; ?></td>
                    <td><?php echo htmlspecialchars($domanda['testo']); ?></td>
                    <td><?php echo htmlspecialchars($domanda['tipo']); ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="azione" value="elimina_domanda">
                            <input type="hidden" name="domanda_id" value="<?php echo $domanda['id']; ?>">
                            <input type="submit" value="Elimina" onclick="return confirm('Sei sicuro di voler eliminare questa domanda?');">
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>