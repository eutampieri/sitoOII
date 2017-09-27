<?php
function getUserData($phone)
{
    $csv = file_get_contents("https://docs.google.com/spreadsheets/d/1Iv8y74oteOicW8DCUi7BEHrMfldZZTHeZy0GmOao2aA/export?format=csv&id=1Iv8y74oteOicW8DCUi7BEHrMfldZZTHeZy0GmOao2aA");
    $rows = explode("\n", $csv);
    $index = false;
    foreach($rows as $row)
    {
        if(!$index)
        {
            $index = true;
            continue;
        }
        $data = explode(",", $row);
        if($data[3] == $phone) return ["cognome" => data[0], "nome" => data[1], "classe" => data[2]];
    }
}
$query = <<<EOF
CREATE TABLE Utenti (Username TEXT, Password TEXT, CMSUser TEXT, CMSPwd TEXT, Email TEXT, Cellulare TEXT, Nome TEXT, Cognome TEXT, Classe TEXT);
CREATE TABLE Posts(Titolo TEXT, Contenuto TEXT, Data INTEGER, Autore TEXT);
CREATE TABLE Risorse(Nome TEXT, File BLOB, Autore TEXT, Data INTEGER)";
CREATE TABLE Notifiche(Username TEXT, JSON TEXT);
CREATE TABLE Sessioni (ID TEXT, Username TEXT);
EOF;

$createDB = false;
if(!is_file("res/db.sqlite")) $createDB = true;
$database = new PDO("sqlite:res/db.sqlite");
$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if($createDB)
{
    $stmt = $database->prepare($query); // statement
    $stmt->execute();
}
$datiUtente=getUserData($_POST["telefono"]);
$qry="INSERT INTO Utenti VALUES(:username , :password , :userCMS , :passwordCMS , :email , :telefono , :nome , :cognome , :classe)";
$stmt=$database->prepare($qry);
$stmt->bindParam(':username', $_POST["username"]);
$stmt->bindParam(':password', $_POST["password"]);
$stmt->bindParam(':userCMS', $_POST["userCMS"]);
$stmt->bindParam(':passwordCMS', $_POST["passwordCMS"]);
$stmt->bindParam(':email', $_POST["email"]);
$stmt->bindParam(':telefono', $_POST["telefono"]);
$stmt->bindParam(':nome', $datiUtente["nome"]);
$stmt->bindParam(':cognome', $datiUtente["cognome"]);
$stmt->bindParam(':classe', $datiUtente["classe"]);
$stmt->execute();
$sessID=uniqid("sess");
setcookie("sessione",$sessID);
$qry="INSERT INTO Sessioni VALUES(:id , :user)";
$stmt->bindParam(':id', $sessID);
$stmt->bindParam(':user', $_POST["username"]);
header("Location: index.html");
// $stmt->bindParam(parameter, variable);