<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
<body>
    <form action="new_domanda.php" method="post">
        <input type="text" name="domanda" placeholder="Domanda">
        <input type="text" name="risposta1" placeholder="Risposta 1"><input type="checkbox" name="risposta1" id="risposta1">
        <input type="text" name="risposta2" placeholder="Risposta 2"><input type="checkbox" name="risposta2" id="risposta2">
        <input type="text" name="risposta3" placeholder="Risposta 3"><input type="checkbox" name="risposta3" id="risposta3">
        <input type="text" name="risposta4" placeholder="Risposta 4"><input type="checkbox" name="risposta4" id="risposta4">
        <input type="submit" value="Aggiungi domanda" name="aggiungi_domanda">
    </form>
</body>
</html>