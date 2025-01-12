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

// Verifica se l'utente ha già svolto il test
$sql = "SELECT * FROM svolgimento_test WHERE test_id = ? AND utente_id = (SELECT id FROM utente WHERE login = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $test_id, $utente);
$stmt->execute();
$result = $stmt->get_result();
$test_svolto = $result->num_rows > 0;

// Recupera le informazioni del test
$sql = "SELECT titolo, descrizione FROM test WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $test_id);
$stmt->execute();
$test_info = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dettagli Test</title>
    <link rel="stylesheet" href="css/style_dettagli_test.css">
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($test_info['titolo']); ?></h1>
        <p class="description"><?php echo htmlspecialchars($test_info['descrizione']); ?></p>
        <nav>
            <a href="index.php">Torna alla Dashboard</a>
        </nav>
    </header>

    <?php if ($test_svolto) { ?>
        <p>Test già svolto.</p>
    <?php } else { ?>
        <div class="centered-form">
            <form method="get" action="svolgi_test.php">
                <input type="hidden" name="id" value="<?php echo $test_id; ?>">
                <input type="submit" value="Svolgi">
            </form>
        </div>
    <?php } ?>
</body>
</html>