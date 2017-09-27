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
$query = "CREATE TABLE Utenti (Username TEXT, Password TEXT, )";
