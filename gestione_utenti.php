<?php
session_start();

// Verifica sessione attiva
if (!isset($_SESSION["utente"]) || $_SESSION["ruolo"] !== "docente") {
    header("Location: login.php");
    exit;
}

// Connessione al database
$servername = "localhost";
$username = "root";
$password = "";
$database = "quiz";
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Funzionalità CRUD per gli utenti
if (isset($_POST['azione'])) {
    $azione = $_POST['azione'];

    if ($azione === 'crea' && !empty($_POST['nome']) && !empty($_POST['cognome']) && !empty($_POST['login']) && !empty($_POST['password']) && !empty($_POST['codice_fiscale']) && !empty($_POST['ruolo'])) {
        $nome = htmlspecialchars($_POST['nome']);
        $cognome = htmlspecialchars($_POST['cognome']);
        $login = htmlspecialchars($_POST['login']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $codice_fiscale = htmlspecialchars($_POST['codice_fiscale']);
        $ruolo = htmlspecialchars($_POST['ruolo']);
        
        $sql = "INSERT INTO utente (nome, cognome, login, password, codice_fiscale, ruolo) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $nome, $cognome, $login, $password, $codice_fiscale, $ruolo);
        $stmt->execute();
    } elseif ($azione === 'elimina' && !empty($_POST['utente_id'])) {
        $utente_id = intval($_POST['utente_id']);
        $sql = "DELETE FROM utente WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $utente_id);
        $stmt->execute();
    }
}

// Recupera tutti gli utenti
$sql = "SELECT * FROM utente";
$result = $conn->query($sql);
$utente_list = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Utenti</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
<body>
    <header>
        <h1>Gestione Utenti</h1>
        <nav>
            <a href="index.php">Torna alla Dashboard</a>
        </nav>
    </header>

    <h2>Aggiungi Nuovo Utente</h2>
    <form method="post">
        <input type="hidden" name="azione" value="crea">

        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required><br>

        <label for="cognome">Cognome:</label>
        <input type="text" id="cognome" name="cognome" required><br>

        <label for="login">Login:</label>
        <input type="text" id="login" name="login" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <label for="codice_fiscale">Codice Fiscale:</label>
        <input type="text" id="codice_fiscale" name="codice_fiscale" required><br>

        <label for="ruolo">Ruolo:</label>
        <select id="ruolo" name="ruolo" required>
            <option value="studente">Studente</option>
            <option value="docente">Docente</option>
        </select><br>

        <input type="submit" value="Aggiungi Utente">
    </form>

    <h2>Lista Utenti</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Cognome</th>
                <th>Login</th>
                <th>Codice Fiscale</th>
                <th>Ruolo</th>
                <th>Azioni</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($utente_list as $utente) { ?>
                <tr>
                    <td><?php echo $utente['id']; ?></td>
                    <td><?php echo htmlspecialchars($utente['nome']); ?></td>
                    <td><?php echo htmlspecialchars($utente['cognome']); ?></td>
                    <td><?php echo htmlspecialchars($utente['login']); ?></td>
                    <td><?php echo htmlspecialchars($utente['codice_fiscale']); ?></td>
                    <td><?php echo htmlspecialchars($utente['ruolo']); ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="azione" value="elimina">
                            <input type="hidden" name="utente_id" value="<?php echo $utente['id']; ?>">
                            <input type="submit" value="Elimina" onclick="return confirm('Sei sicuro di voler eliminare questo utente?');">
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
