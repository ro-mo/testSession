<?php
session_start();
require 'functions.php';
checkLogin();

$conn = connectDB();
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

if (isset($_POST['submit_test'])) {
    $test_id = intval($_POST['test_id']);
    foreach ($_POST['risposte'] as $domanda_id => $risposta) {
        salvaRisposta($conn, $_SESSION['utente'], $domanda_id, $risposta);
    }
    header("Location: index.php");
    exit();
}

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
$stmt->bind_param("s", $_SESSION['utente']);
$stmt->execute();
$result = $stmt->get_result();
$utente_info = $result->fetch_assoc();

if ($test_info['classe'] !== $utente_info['classe']) {
    die("Non sei autorizzato a svolgere questo test.");
}

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
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
<body>
    <header>
        <h1>Svolgi Test</h1>
        <nav>
            <a href="index.php">Torna alla Dashboard</a>
        </nav>
    </header>

    <form method="post">
        <input type="hidden" name="test_id" value="<?php echo $test_id; ?>">
        <?php foreach ($domande as $domanda) { ?>
            <div>
                <p><?php echo htmlspecialchars($domanda['testo']); ?></p>
                <?php if ($domanda['tipo'] === 'multipla') {
                    $sql = "SELECT * FROM risposta WHERE domanda_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $domanda['id']);
                    $stmt->execute();
                    $risposte = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                    foreach ($risposte as $risposta) { ?>
                        <input type="radio" name="risposte[<?php echo $domanda['id']; ?>]" value="<?php echo $risposta['id']; ?>">
                        <label><?php echo htmlspecialchars($risposta['testo']); ?></label><br>
                    <?php }
                } else { ?>
                    <textarea name="risposte[<?php echo $domanda['id']; ?>]"></textarea><br>
                <?php } ?>
            </div>
        <?php } ?>
        <input type="submit" name="submit_test" value="Invia Risposte">
    </form>
</body>
</html>