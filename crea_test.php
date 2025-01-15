<?php
session_start();
require 'functions.php';
checkLogin();

$conn = connectDB();
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Funzionalità CRUD per i test
if (isset($_POST['azione'])) {
    $azione = $_POST['azione'];
    if ($azione === 'crea' && !empty($_POST['titolo']) && !empty($_POST['descrizione']) && !empty($_POST['classe'])) {
        $titolo = htmlspecialchars($_POST['titolo']);
        $descrizione = htmlspecialchars($_POST['descrizione']);
        $classe = htmlspecialchars($_POST['classe']);
        $visibile = isset($_POST['visibile']) ? 1 : 0;
        $creatore = $_SESSION['utente'];

        $domande = [];
        if (!empty($_POST['domande'])) {
            foreach ($_POST['domande'] as $domanda) {
                $domanda_testo = htmlspecialchars($domanda['testo']);
                $domanda_tipo = htmlspecialchars($domanda['tipo']);
                $risposte = [];
                if ($domanda_tipo === 'multipla' && !empty($domanda['risposte'])) {
                    foreach ($domanda['risposte'] as $risposta) {
                        $corretta = isset($risposta['corretta']) ? 1 : 0;
                        $risposte[] = [
                            'testo' => htmlspecialchars($risposta['testo']),
                            'corretta' => $corretta
                        ];
                    }
                }
                $domande[] = [
                    'testo' => $domanda_testo,
                    'tipo' => $domanda_tipo,
                    'risposte' => $risposte
                ];
            }
        }

        if (createTest($conn, $titolo, $descrizione, $creatore, $classe, $visibile, $domande)) {
            header("Location: modifica_test.php?id=" . $conn->insert_id);
            exit();
        } else {
            $errore = "Errore nella creazione del test. Riprova.";
        }
    }
}

// Recupera tutti i test creati dal docente
$creatore = $_SESSION['utente'];
$sql = "SELECT * FROM test WHERE creatore = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $creatore);
$stmt->execute();
$result = $stmt->get_result();
$test_list = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Test</title>
    <link rel="stylesheet" href="css/style_crea_test.css">
</head>
<body>
    <header>
        <h1>Gestione Test</h1>
        <nav>
            <a href="index.php">Torna alla Dashboard</a>
        </nav>
    </header>

    <h2>Crea un Nuovo Test</h2>
    <?php if (isset($errore)) echo "<p style='color:red;'>$errore</p>"; ?>
    <form method="post">
        <input type="hidden" name="azione" value="crea">
        <label for="titolo">Titolo:</label>
        <input type="text" id="titolo" name="titolo" required><br>

        <label for="descrizione">Descrizione:</label>
        <textarea id="descrizione" name="descrizione" required></textarea><br>

        <label for="classe">Classe:</label>
        <input type="text" id="classe" name="classe" required><br>

        <label for="visibile">Visibile agli studenti:</label>
        <input type="checkbox" name="visibile" id="visibile"><br>

        <input type="submit" value="Crea Test">
    </form>

    <h2>Lista Test</h2>
    <?php if (count($test_list) > 0) { ?>
        <table>
            <thead>
                <tr>
                    <th>Titolo</th>
                    <th>Descrizione</th>
                    <th>Classe</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($test_list as $test) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($test['titolo']); ?></td>
                        <td><?php echo htmlspecialchars($test['descrizione']); ?></td>
                        <td><?php echo htmlspecialchars($test['classe']); ?></td>
                        <td>
                            <form method="get" action="modifica_test.php" style="display:inline; padding:0;">
                                <input type="hidden" name="id" value="<?php echo $test['id']; ?>">
                                <input type="submit" value="Modifica" class="button-yellow">
                            </form>
                            <form method="post" style="display:inline; padding:0;">
                                <input type="hidden" name="azione" value="elimina">
                                <input type="hidden" name="test_id" value="<?php echo $test['id']; ?>">
                                <input type="submit" value="Elimina" onclick="return confirm('Sei sicuro di voler eliminare questo test?');">
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p>Non ci sono test creati.</p>
    <?php } ?>
</body>
</html>