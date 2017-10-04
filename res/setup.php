<?php
$query = <<<EOF
CREATE TABLE Utenti (Username TEXT, Password TEXT, CMSUser TEXT, Email TEXT, Cellulare TEXT, Nome TEXT, Cognome TEXT, Classe TEXT);
CREATE TABLE Post(Titolo TEXT, Contenuto TEXT, Data INTEGER, Autore TEXT);
CREATE TABLE Risorse(Nome TEXT, File BLOB, Autore TEXT, Data INTEGER)";
CREATE TABLE Notifiche(Username TEXT, JSON TEXT);
CREATE TABLE Sessioni (ID TEXT, Username TEXT);
CREATE TABLE Tutor (CMSUser TEXT);
CREATE TABLE APIKeys (Servizio TEXT, Chiave TEXT);
CREATE TABLE RifClassifica (CMSUser TEXT);
EOF;

$createDB = false;
if(!is_file("db.sqlite")) $createDB = true;
$database = new PDO("sqlite:res/db.sqlite");
$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if($createDB)
{
    $stmt = $database->prepare($query); // statement
    $stmt->execute();
}
elseif(isset($_POST["action"])){
    $listaTutor=json_decode($_POST["tutors"]);
    $qry="INSERT INTO Tutor VALUES (:u);";
    foreach ($listaTutor as $t) {
        $stmt=$database->prepare($qry);
        $stmt->bindParam(":u", $t);
        $stmt->execute();
    }
    $listaPDR=json_decode($_POST["classpdr"]);
    $qry="INSERT INTO RifClassifica VALUES (:u);"
    foreach ($listaPDR as $t) {
        $stmt=$database->prepare($qry);
        $stmt->bindParam(":u", $t);
        $stmt->execute();
    }
    $qry=<<<EOF
    INSERT INTO APIKeys VALUES ("Mail.user", :mu);
    INSERT INTO APIKeys VALUES ("Mail.pwd", :mp);
    INSERT INTO APIKeys VALUES ("Mail.SMTP", :ms);
    INSERT INTO APIKeys VALUES ("Mail.porta", :mt);
    INSERT INTO APIKeys VALUES ("TG.botKey", :tb);
    INSERT INTO APIKeys VALUES ("TG.ch", :tc);
EOF;
    $stmt=$database->prepare($qry);
    $stmt->bindParam(":mu", $_POST["mail.user"]);
    $stmt->bindParam(":mp", $_POST["mail.pwd"]);
    $stmt->bindParam(":ms", $_POST["mail.smtp"]);
    $stmt->bindParam(":mt", $_POST["mail.porta"]);
    $stmt->bindParam(":tb", $_POST["tg.bot"]);
    $stmt->bindParam(":tc", $_POST["tg.ch"]);
}
else{
    die();
}
?>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="css/stile.css">
        <script src="js/main.js"></script>
        <script src="js/setup.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/awesomplete.css" />
	    <script src="js/awesomplete.min.js" async></script>
	    <title>Configurazione sito allenamenti OII</title>
    </head>
    <body onload="loadWrapper()">
        <h1>Configurazione iniziale</h1>
        <div class="leftPart"></div>
        <div class="mainPart">
            <form class="post">
                <h2>Tutor</h2>
                <input type="hidden" id="tutors" name="tutors">
                <ul id="listaTutor">
                    <li><input id="tutor0" onchange="serializeTutors()" placeholder="Ricerca per nome o username" disabled></li>
                </ul>
                <a class="button" onclick="addTutor()">Aggiungi un altro tutor</a>
                <h2>Classifica</h2>
                <p>&Egrave; possibile specificare gli utenti da includere nella classifica anche se non sono registrati</p>
                <input type="hidden" id="classpdr" name="classpdr">
                <ul id="listaPDR">
                    <li><input id="PDR0" onchange="serializePDR()" placeholder="Ricerca per nome o username" disabled></li>
                </ul>
                <a class="button" onclick="addPDR()">Aggiungi un altro utente</a>
                <h2>Account di posta elettronica</h2>
                Username: <input type="email" name="mail.user"><br>
                Password: <input type="password" name="mail.pwd"><br>
                Server SMTP: <input type="text" name="mail.smtp"><br>
                Porta server SMTP: <input type="text" name="mail.porta"><br>
                <h2>Telegram</h2>
                Chiave del bot amministratore del canale: <input type="text" name="tg.bot"><br>
                Nome del canale: <input type="text" name="tg.ch"><br>
            </form>
        </div>
</html>