<?php
$query = <<<EOF
CREATE TABLE Utenti (Username TEXT, Password TEXT, CMSUser TEXT, Email TEXT, Cellulare TEXT, Nome TEXT, Cognome TEXT, Classe TEXT)
CREATE TABLE Post(Titolo TEXT, Contenuto TEXT, Data INTEGER, Autore TEXT)
CREATE TABLE Risorse(Nome TEXT, File BLOB, Autore TEXT, Data INTEGER)
CREATE TABLE Notifiche(Username TEXT, JSON TEXT)
CREATE TABLE Sessioni (ID TEXT, Username TEXT)
CREATE TABLE Tutor (CMSUser TEXT)
CREATE TABLE APIKeys (Servizio TEXT, Chiave TEXT)
CREATE TABLE RifClassifica (CMSUser TEXT)
EOF;

$createDB = false;
if(!is_file("db.sqlite")) $createDB = true;
$database = new PDO("sqlite:db.sqlite");
$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if($createDB)
{
    foreach(explode("\n",$query) as $q){
        $stmt = $database->prepare($q);
        $stmt->execute();
    }
}
elseif(isset($_POST["action"])){
    $listaTutor=json_decode($_POST["tutors"],true);
    $qry="INSERT INTO Tutor VALUES (:u);";
    foreach ($listaTutor as $t) {
        $stmt=$database->prepare($qry);
        $stmt->bindParam(":u", $t);
        $stmt->execute();
    }
    $listaPDR=json_decode($_POST["classpdr"],true);
    $qry="INSERT INTO RifClassifica VALUES (:u);";
    foreach ($listaPDR as $t) {
        $stmt=$database->prepare($qry);
        $stmt->bindParam(":u", $t);
        $stmt->execute();
    }
    $qry='INSERT INTO APIKeys VALUES ("Mail.user", :mu);';
    $stmt=$database->prepare($qry);
    $stmt->bindParam(":mu", $_POST["mail_user"]);    
    $stmt->execute();
    $qry='INSERT INTO APIKeys VALUES ("Mail_pwd", :mp);';
    $stmt=$database->prepare($qry);
    $stmt->bindParam(":mp", $_POST["mail_pwd"]);
    $stmt->execute();
    $qry='INSERT INTO APIKeys VALUES ("Mail.SMTP", :ms);';
    $stmt=$database->prepare($qry);
    $stmt->bindParam(":ms", $_POST["mail_smtp"]);
    $stmt->execute();
    $qry='INSERT INTO APIKeys VALUES ("Mail.porta", :mt);';
    $stmt=$database->prepare($qry);
    $stmt->bindParam(":mt", $_POST["mail_porta"]);
    $stmt->execute();
    $qry='INSERT INTO APIKeys VALUES ("TG.botKey", :tb);';
    $stmt=$database->prepare($qry);
    $stmt->bindParam(":tb", $_POST["tg_bot"]);
    $stmt->execute();
    $qry='INSERT INTO APIKeys VALUES ("TG.ch", :tc);';
    $stmt=$database->prepare($qry);
    $stmt->bindParam(":tc", $_POST["tg_ch"]);
    $stmt->execute();
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
            <form class="post" method="POST">
                <input type="hidden" name="action" value="setup">
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
                Username: <input type="text" name="mail_user"><br>
                Password: <input type="password" name="mail_pwd"><br>
                Server SMTP: <input type="text" name="mail_smtp"><br>
                Porta server SMTP: <input type="text" name="mail_porta"><br>
                <h2>Telegram</h2>
                Chiave del bot amministratore del canale: <input type="text" name="tg_bot"><br>
                Nome del canale: <input type="text" name="tg_ch"><br>
                <input type="submit" value="Salva">
            </form>
        </div>
</html>