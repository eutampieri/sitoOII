<?php
$query = <<<EOF
CREATE TABLE Utenti (Username TEXT, Password TEXT, CMSUser TEXT, Email TEXT, Cellulare TEXT, Nome TEXT, Cognome TEXT, Classe TEXT);
CREATE TABLE Posts(Titolo TEXT, Contenuto TEXT, Data INTEGER, Autore TEXT);
CREATE TABLE Risorse(Nome TEXT, File BLOB, Autore TEXT, Data INTEGER)";
CREATE TABLE Notifiche(Username TEXT, JSON TEXT);
CREATE TABLE Sessioni (ID TEXT, Username TEXT);
CREATE TABLE Tutor (CMSUser TEXT, Nome TEXT, Cognome TEXT, Classe TEXT);
CREATE TABLE APIKeys (Servizio TEXT, Chiave TEXT);
EOF;

/*$createDB = false;
if(!is_file("res/db.sqlite")) $createDB = true;
$database = new PDO("sqlite:res/db.sqlite");
$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if($createDB)
{
    $stmt = $database->prepare($query); // statement
    $stmt->execute();
}
elseif(isset($_POST["action"])){
    //
}
else{
    die();
}*/
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
            <div class="post">
                <h2>Tutor</h2>
                <input id="tutor0" placeholder="Ricerca per nome o username" disabled>
            </div>
        </div>
</html>