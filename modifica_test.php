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
            foreach ($_POST['risposte'] as $risposta) {
                if (!empty($risposta['testo'])) {
                    $risposte[] = [
                        'testo' => htmlspecialchars($risposta['testo']),
                        'corretta' => isset($risposta['corretta']) ? 1 : 0
                    ];
                }
            }
        }
        addDomanda($conn, $test_id, $testo, $tipo, $risposte);
    } elseif ($azione === 'elimina_domanda' && !empty($_POST['domanda_id'])) {
        $domanda_id = intval($_POST['domanda_id']);
        
        // Elimina le risposte utente associate alle risposte della domanda
        $sql = "DELETE ru FROM risposta_utente ru
                JOIN risposta r ON ru.risposta_id = r.id
                WHERE r.domanda_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $domanda_id);
        $stmt->execute();

        // Elimina le risposte della domanda
        $sql = "DELETE FROM risposta WHERE domanda_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $domanda_id);
        $stmt->execute();

        // Elimina la domanda
        $sql = "DELETE FROM domanda WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $domanda_id);
        $stmt->execute();
    } elseif ($azione === 'aggiorna_visibilita') {
        $visibile = isset($_POST['visibile']) ? 1 : 0;
        $sql = "UPDATE test SET visibile = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $visibile, $test_id);
        $stmt->execute();
    }
}

// Recupera tutte le domande del test
$sql = "SELECT * FROM domanda WHERE test_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $test_id);
$stmt->execute();
$domande = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Recupera le informazioni del test
$sql = "SELECT titolo, visibile FROM test WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $test_id);
$stmt->execute();
$test_info = $stmt->get_result()->fetch_assoc();
$titolo_test = $test_info['titolo'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Test</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
    <script>
        function toggleRisposteField() {
            var tipo = document.getElementById("tipo").value;
            var risposteField = document.getElementById("risposteField");
            if (tipo === "libera") {
                risposteField.style.display = "none";
            } else {
                risposteField.style.display = "block";
            }
        }

        function aggiungiRisposta() {
            var risposteField = document.getElementById("risposteField");
            var nuovaRisposta = document.createElement("div");
            nuovaRisposta.className = "risposta";
            nuovaRisposta.innerHTML = `
                <label>Risposta:</label>
                <input type="text" name="risposte[][testo]">
                <label>Corretta:</label>
                <input type="checkbox" name="risposte[][corretta]">
            `;
            risposteField.insertBefore(nuovaRisposta, risposteField.lastElementChild);
        }
    </script>
    <style>
        .risposta {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .risposta label {
            margin-right: 10px;
        }
        .risposta input[type="text"] {
            width: 200px;
            margin-right: 20px; /* Aggiunge spazio tra il campo di testo e il testo "Corretta" */
        }
        .risposta input[type="checkbox"] {
            margin-left: 10px;
        }
        #risposteField {
            margin-bottom: 20px;
        }
        .section-title {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Modifica Test: <?php echo htmlspecialchars($titolo_test); ?></h1>
        <nav>
            <a href="crea_test.php">Torna indietro</a>
        </nav>
    </header>

    <h2 class="section-title">Aggiungi Domanda</h2>
    <form method="post">
        <input type="hidden" name="azione" value="aggiungi_domanda">
        <label for="testo">Domanda:</label>
        <input type="text" name="testo" id="testo" required><br>

        <label for="tipo">Tipo:</label>
        <select name="tipo" id="tipo" onchange="toggleRisposteField()" required>
            <option value="libera">Libera</option>
            <option value="multipla">Multipla</option>
        </select><br>

        <div id="risposteField" style="display: none;">
            <div class="risposta">
                <label>Risposta:</label>
                <input type="text" name="risposte[][testo]">
                <label>Corretta:</label>
                <input type="checkbox" name="risposte[][corretta]">
            </div>
            <div class="risposta">
                <label>Risposta:</label>
                <input type="text" name="risposte[][testo]">
                <label>Corretta:</label>
                <input type="checkbox" name="risposte[][corretta]">
            </div>
            <button type="button" onclick="aggiungiRisposta()">Aggiungi Risposta</button>
        </div>

        <input type="submit" value="Aggiungi Domanda">
    </form>

    <h2 class="section-title">Visibilità del Test</h2>
    <form method="post">
        <input type="hidden" name="azione" value="aggiorna_visibilita">
        <label for="visibile">Visibile agli studenti:</label>
        <input type="checkbox" name="visibile" id="visibile" value="1" <?php echo $test_info['visibile'] ? 'checked' : ''; ?>><br>
        <input type="submit" value="Aggiorna Visibilità">
    </form>

    <h2 class="section-title">Domande Esistenti</h2>
    <ul>
        <?php foreach ($domande as $domanda) { ?>
            <li>
                <?php echo htmlspecialchars($domanda['testo']); ?> (<?php echo htmlspecialchars($domanda['tipo']); ?>)
                <form method="post" style="display:inline;">
                    <input type="hidden" name="azione" value="elimina_domanda">
                    <input type="hidden" name="domanda_id" value="<?php echo $domanda['id']; ?>">
                    <input type="submit" value="Elimina">
                </form>
            </li>
        <?php } ?>
    </ul>
</body>
</html>