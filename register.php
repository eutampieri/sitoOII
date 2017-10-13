<?php
if(is_file("res/db.sqlite")){
    $database = new PDO("sqlite:res/db.sqlite");
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    
}
else{
    $database=null;
}
/*function getUserData($phone)
{
    $csv = file_get_contents(file_get_contents("res/gSheet.apikey"));
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
        if($data[3] == $phone) return ["cognome" => $data[0], "nome" => $data[1], "classe" => $data[2]];
    }
    return ["cognome" =>null, "nome" => null, "classe" => null];
}
$datiUtente=getUserData($_POST["telefono"]);*/
$qry="INSERT INTO Utenti VALUES(:username , :password , :userCMS  , :email , :nome , :cognome , :classe)";
$stmt=$database->prepare($qry);
$stmt->bindParam(':username', $_POST["username"]);
$stmt->bindParam(':password', password_hash($_POST["password"], PASSWORD_DEFAULT));
$stmt->bindParam(':userCMS', $_POST["userCMS"]);
$stmt->bindParam(':email', $_POST["email"]);
$stmt->bindParam(':nome', $_POST["nome"]);
$stmt->bindParam(':cognome', $_POST["cognome"]);
$stmt->bindParam(':classe', $_POST["classe"]);
$stmt->execute();
$sessID=uniqid("sess");
setcookie("sessione",$sessID);
$qry="INSERT INTO Sessioni VALUES (:id , :user)";
$stmt=$database->prepare($qry);
$stmt->bindParam(':id', $sessID);
$stmt->bindParam(':user', $_POST["username"]);
$stmt->execute();
header("Location: index.html#ok&Registrazione%20avvenuta%20con%20sucesso");
// $stmt->bindParam(parameter, variable);
