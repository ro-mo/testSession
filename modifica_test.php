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
        
        $sql = "INSERT INTO domanda (test_id, testo, tipo) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $test_id, $testo, $tipo);
        $stmt->execute();
        $domanda_id = $conn->insert_id;

        if ($tipo === 'multipla' && !empty($risposte)) {
            $sql = "INSERT INTO risposta (domanda_id, testo, corretta) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            foreach ($risposte as $risposta) {
                $stmt->bind_param("isi", $domanda_id, $risposta['testo'], $risposta['corretta']);
                $stmt->execute();
            }
        }

    } elseif ($azione === 'elimina_domanda' && !empty($_POST['domanda_id'])) {
        $domanda_id = intval($_POST['domanda_id']);
        
        $sql = "DELETE ru FROM risposta_utente ru
                JOIN risposta r ON ru.risposta_id = r.id
                WHERE r.domanda_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $domanda_id);
        $stmt->execute();

        $sql = "DELETE FROM risposta WHERE domanda_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $domanda_id);
        $stmt->execute();

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
    } elseif ($azione === 'aggiorna_test' && !empty($_POST['titolo']) && !empty($_POST['descrizione'])) {
        $titolo = htmlspecialchars($_POST['titolo']);
        $descrizione = htmlspecialchars($_POST['descrizione']);
        $sql = "UPDATE test SET titolo = ?, descrizione = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $titolo, $descrizione, $test_id);
        $stmt->execute();
    } elseif ($azione === 'modifica_domanda' && !empty($_POST['domanda_id']) && !empty($_POST['testo'])) {
        $domanda_id = intval($_POST['domanda_id']);
        $testo = htmlspecialchars($_POST['testo']);
        $sql = "UPDATE domanda SET testo = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $testo, $domanda_id);
        $stmt->execute();

        if (!empty($_POST['risposte'])) {
            foreach ($_POST['risposte'] as $risposta_id => $risposta) {
                $testo_risposta = htmlspecialchars($risposta['testo']);
                $corretta = isset($risposta['corretta']) ? 1 : 0;
                $sql = "UPDATE risposta SET testo = ?, corretta = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sii", $testo_risposta, $corretta, $risposta_id);
                $stmt->execute();
            }
        }
    }
}

$sql = "SELECT * FROM domanda WHERE test_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $test_id);
$stmt->execute();
$domande = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$sql = "SELECT titolo, descrizione, visibile FROM test WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $test_id);
$stmt->execute();
$test_info = $stmt->get_result()->fetch_assoc();
$titolo_test = $test_info['titolo'];
$descrizione_test = $test_info['descrizione'];
$visibile_test = $test_info['visibile'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Test</title>
    <link rel="stylesheet" href="css/style_modifica_test.css">
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
                <div style="justify-content: space-between;">
                <label>Risposta:</label>
                <input type="text" name="risposte[][testo]">
                <label>Corretta:</label>
                <input type="checkbox" name="risposte[][corretta]">
                </div>
            `;
            risposteField.insertBefore(nuovaRisposta, risposteField.lastElementChild);
        }
    </script>
</head>
<body>
    <header>
        <h1>Modifica Test: <?php echo htmlspecialchars($titolo_test); ?></h1>
        <nav>
            <a href="crea_test.php">Torna indietro</a>
        </nav>
    </header>

    <h2 class="section-title">Modifica Titolo e Descrizione</h2>
    <form method="post">
        <input type="hidden" name="azione" value="aggiorna_test">
        <label for="titolo">Titolo:</label>
        <input type="text" name="titolo" id="titolo" value="<?php echo htmlspecialchars($titolo_test); ?>" required><br>

        <label for="descrizione">Descrizione:</label>
        <textarea name="descrizione" id="descrizione" required><?php echo htmlspecialchars($descrizione_test); ?></textarea><br>

        <input type="submit" value="Aggiorna Test">
    </form>

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
        <input type="checkbox" name="visibile" id="visibile" value="1" <?php echo $visibile_test ? 'checked' : ''; ?>><br>
        <input type="submit" value="Aggiorna Visibilità">
    </form>

    <h2 class="section-title">Domande Esistenti</h2>
    <ul>
    <?php foreach ($domande as $domanda) { ?>
        <li>
            <form method="post" class="form-inline">
                <input type="hidden" name="azione" value="modifica_domanda">
                <input type="hidden" name="domanda_id" value="<?php echo $domanda['id']; ?>">
                <label for="testo_<?php echo $domanda['id']; ?>">Domanda:</label>
                <input type="text" name="testo" id="testo_<?php echo $domanda['id']; ?>" value="<?php echo htmlspecialchars($domanda['testo']); ?>" required><br>

                <?php if ($domanda['tipo'] === 'multipla') {
                    $sql = "SELECT * FROM risposta WHERE domanda_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $domanda['id']);
                    $stmt->execute();
                    $risposte = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                    foreach ($risposte as $risposta) { ?>
                        <div class="risposta" style="display:inline-block">
                            <label>Risposta:</label>
                            <input type="text" name="risposte[<?php echo $risposta['id']; ?>][testo]" value="<?php echo htmlspecialchars($risposta['testo']); ?>">
                            <input type="checkbox" name="risposte[<?php echo $risposta['id']; ?>][corretta]" <?php echo $risposta['corretta'] ? 'checked' : ''; ?>>
                        </div>
                    <?php }
                } ?>

                <input type="submit" value="Modifica Domanda">
            </form>
            <form method="post" class="form-inline">
                <input type="hidden" name="azione" value="elimina_domanda">
                <input type="hidden" name="domanda_id" value="<?php echo $domanda['id']; ?>">
                <input type="submit" value="Elimina">
            </form>
        </li>
    <?php } ?>
    </ul>
</body>
</html>