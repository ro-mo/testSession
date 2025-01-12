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
        salvaRisposta($<?php
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
        salvaRisposta($