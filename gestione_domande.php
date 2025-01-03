<?php
require 'functions.php';
checkLogin();

$conn = connectDB();
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

if (isset($_POST['azione'])) {
    $azione = $_POST['azione'];
    if ($azione === 'crea' && !empty($_POST['test_id']) && !empty($_POST['testo']) && !empty($_POST['tipo'])) {
        $test_id = intval($_POST['test_id']);
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
    } elseif ($azione === 'elimina' && !empty($_POST['domanda_id'])) {
        $domanda_id = intval($_POST['domanda_id']);
        $sql = "DELETE FROM domanda WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $domanda_id);
        $stmt->execute();
    }
}

// Recupera tutte le domande
$sql = "SELECT * FROM domanda";
$result = $conn->query($sql);
$domanda_list = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Domande</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
<body>
    <header>
        <h1>Gestione Domande</h1>
        <nav>
            <a href="index.php">Torna alla Dashboard</a>
        </nav>
    </header>

    <h2>Crea una Nuova Domanda</h2>
    <form method="post">
        <input type="hidden" name="azione" value="crea">
        <label for="test_id">ID Test:</label>
        <input type="number" id="test_id" name="test_id" required><br>

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

        <input type="submit" value="Crea Domanda">
    </form>

    <h2>Lista Domande</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Test ID</th>
                <th>Testo</th>
                <th>Tipo</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($domanda_list as $domanda) { ?>
                <tr>
                    <td><?php echo $domanda['id']; ?></td>
                    <td><?php echo htmlspecialchars($domanda['test_id']); ?></td>
                    <td><?php echo htmlspecialchars($domanda['testo']); ?></td>
                    <td><?php echo htmlspecialchars($domanda['tipo']); ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="azione" value="elimina">
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