<?php
session_start();
require 'functions.php';
checkLogin();

$conn = connectDB();
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$utente = $_SESSION['utente'];
$test_id = intval($_GET['id']);

// Verifica che l'utente appartenga alla stessa classe del test
$sql = "SELECT classe FROM test WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $test_id);
$stmt->execute();
$result = $stmt->get_result();
$test_info = $result->fetch_assoc();

$sql = "SELECT classe FROM utente WHERE login = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $utente);
$stmt->execute();
$result = $stmt->get_result();
$utente_info = $result->fetch_assoc();

if ($test_info['classe'] !== $utente_info['classe']) {
    die("Non sei autorizzato a svolgere questo test.");
}

// Inserisci un record nella tabella svolgimento_test se non esiste già
$sql = "SELECT * FROM svolgimento_test WHERE test_id = ? AND utente_id = (SELECT id FROM utente WHERE login = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $test_id, $utente);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $sql = "INSERT INTO svolgimento_test (test_id, utente_id, data_inizio) VALUES (?, (SELECT id FROM utente WHERE login = ?), NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $test_id, $utente);
    $stmt->execute();
}

if (isset($_POST['submit_test'])) {
    foreach ($_POST['risposte'] as $domanda_id => $risposta) {
        salvaRisposta($conn, $utente, $domanda_id, $risposta);
    }
    header("Location: index.php");
    exit();
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Svolgi Test</title>
    <link rel="stylesheet" href="css/style_svolgi_test.css">
</head>
<body>
    <header>
        <h1>Svolgi Test</h1>
        <nav>
            <input type="button" value="Torna alla Dashboard" onclick="location.href='index.php';">
        </nav>
    </header>

    <form method="post">
        <?php foreach ($domande as $domanda) { ?>
            <div class="domanda">
                <label><?php echo htmlspecialchars($domanda['testo']); ?></label>
                <?php if ($domanda['tipo'] === 'multipla') {
                    $sql = "SELECT * FROM risposta WHERE domanda_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $domanda['id']);
                    $stmt->execute();
                    $risposte = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                    foreach ($risposte as $risposta) { ?>
                        <div class="risposta" style="display: flex; align-items: center;">
                            <input type="radio" name="risposte[<?php echo $domanda['id']; ?>]" value="<?php echo $risposta['id']; ?>" required>
                            <label style="margin-left: 5px;"><?php echo htmlspecialchars($risposta['testo']); ?></label>
                        </div>
                    <?php } } else { ?>
                    <input type="text" name="risposte[<?php echo $domanda['id']; ?>]" required>
                <?php } ?>
            </div>
        <?php } ?>
        <input type="submit" name="submit_test" value="Invia Risposte">
    </form>
</body>
</html>