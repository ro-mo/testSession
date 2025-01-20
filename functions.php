<?php
function connectDB() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "quiz";
    return new mysqli($servername, $username, $password, $database);
}

function checkLogin() {
    if (!isset($_SESSION["utente"])) {
        header("Location: login.php");
        exit();
    }
}

function isDocente($conn, $login) {
    $sql = "SELECT tipo FROM utente WHERE login = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['tipo'] === 'docente';
    }
    return false;
}

function createTest($conn, $titolo, $descrizione, $creatore, $classe, $visibile, $domande) {
    $conn->begin_transaction();
    try {
        $sql = "INSERT INTO test (titolo, descrizione, creatore, classe, visibile) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $titolo, $descrizione, $creatore, $classe, $visibile);
        $stmt->execute();
        $test_id = $conn->insert_id;

        foreach ($domande as $domanda) {
            $sql = "INSERT INTO domanda (test_id, testo, tipo) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $test_id, $domanda['testo'], $domanda['tipo']);
            $stmt->execute();
            $domanda_id = $conn->insert_id;

            if ($domanda['tipo'] === 'multipla' && !empty($domanda['risposte'])) {
                $sql = "INSERT INTO risposta (domanda_id, testo, corretta) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                foreach ($domanda['risposte'] as $risposta) {
                    $stmt->bind_param("isi", $domanda_id, $risposta['testo'], $risposta['corretta']);
                    $stmt->execute();
                }
            }
        }

        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
}

function addDomanda($conn, $test_id, $testo, $tipo, $risposte) {
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
}

function salvaRisposta($conn, $utente, $domanda_id, $risposta) {
    // Recupera l'id dell'utente
    $sql = "SELECT id FROM utente WHERE login = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $utente);
    $stmt->execute();
    $result = $stmt->get_result();
    $utente_info = $result->fetch_assoc();
    $utente_id = $utente_info['id'];

    // Recupera il tipo della domanda
    $sql = "SELECT tipo FROM domanda WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $domanda_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $domanda_info = $result->fetch_assoc();
    $tipo_domanda = $domanda_info['tipo'];

    if ($tipo_domanda === 'multipla') {
        $sql = "INSERT INTO risposta_utente (utente_id, domanda_id, risposta_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $utente_id, $domanda_id, $risposta);
    } else {
        $sql = "INSERT INTO risposta_utente (utente_id, domanda_id, testo_libero) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $utente_id, $domanda_id, $risposta);
    }
    return $stmt->execute();
}

function getRisultatiTest($conn, $test_id, $utente_id = null) {
    $sql = "SELECT d.testo AS domanda, r.testo AS risposta, r.corretta, ru.testo_libero, u.nome, u.cognome
            FROM risposta_utente ru
            JOIN domanda d ON ru.domanda_id = d.id
            LEFT JOIN risposta r ON ru.risposta_id = r.id
            JOIN utente u ON ru.utente_id = u.id
            WHERE d.test_id = ?";
    if ($utente_id) {
        $sql .= " AND u.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $test_id, $utente_id);
    } else {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $test_id);
    }
    $stmt->execute();
    return $stmt->get_result();
}
?>