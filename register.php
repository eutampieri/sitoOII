<?php
function getUserData($phone)
{
    $csv = file_get_contents("https://docs.google.com/spreadsheets/d/1Iv8y74oteOicW8DCUi7BEHrMfldZZTHeZy0GmOao2aA/export?format=csv&id=1Iv8y74oteOicW8DCUi7BEHrMfldZZTHeZy0GmOao2aA");
    $rows = explode("\n", $csv);
    $index = false;
    foreach($rows as $row)
    {
        if(!index)
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
EOF;

$createDB = false;
if(!is_file("res/db.sqlite")) $createDB = true;
$database = new PDO("sqlite:res/db.sqlite");
$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if(createDB)
{
    $stmt = $database->prepare($query); // statement
    $stmt->execute();
}

// $stmt->bindParam(parameter, variable);