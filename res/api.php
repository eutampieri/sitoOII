<?php
if(!is_file("db.sqlite")){
    if(isset($_GET["nodb"])){
        echo "1";
    }
    die();
}
$database = new PDO("sqlite:db.sqlite");
$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
if (isset($_GET["action"])) {
    $azione=$_GET["action"];
} else {
    $azione=$_POST["action"];
}
switch ($azione) {
    case 'userExists':
        $stmt=$database->prepare("SELECT * FROM Utenti WHERE Username = :u");
        $stmt->bindParam(':u', $_POST["username"]);
        $stmt->execute();
        echo strval(count($stmt->fetchAll(PDO::FETCH_ASSOC)));
        break;
    case 'cmsUser':
        $data = array('action' => "login", 'username' =>$_POST["username"] , 'password'=>$_POST["password"], 'keep_signed' => false);
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($data)
            )
        );
        $context  = stream_context_create($options);
        echo file_get_contents("https://cms.di.unipi.it/api/user", false, $context);
        break;
    case "classifica":
        $data = array('action' => "list", 'first' =>$_GET["first"] , 'last'=>$_GET["last"]);
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($data)
            )
        );
        $context  = stream_context_create($options);
        echo file_get_contents("https://cms.di.unipi.it/api/user", false, $context);
        break;
    case "lezioni":
        $stmt=$database->prepare("SELECT * FROM Eventi WHERE Tipo = \"Lezione\"");
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
    case "gare":
        $stmt=$database->prepare("SELECT * FROM Eventi WHERE Tipo = \"Gara\"");
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
    case "isTutor":
        $stmt=$database->prepare("SELECT Username FROM Sessioni WHERE ID = :id AND Username IN (SELECT Utenti.Username FROM Tutor INNER JOIN Utenti ON Tutors.CMSUser = Utenti.Username");
        $stmt->bindParam(":id", $_COOKIE["session"]);
        $stmt->execute();
        echo strval(count($stmt->fetchAll(PDO::FETCH_ASSOC)));
        break;
    case "addEvent":
        $stmt=$database->prepare("SELECT Username FROM Sessioni WHERE ID = :id AND Username IN (SELECT Utenti.Username FROM Tutor INNER JOIN Utenti ON Tutors.CMSUser = Utenti.Username");
        $stmt->bindParam(":id", $_COOKIE["session"]);
        $stmt->execute();
        if(count($stmt->fetchAll(PDO::FETCH_ASSOC))==1){
            $stmt->prepare("INSERT INTO Eventi VALUES (:d , :i , :f , :t )");
            $stmt->bindParam(":d",$_POST["descrizione"]);
            $stmt->bindParam(":i",$_POST["inizio"]);
            $stmt->bindParam(":f",$_POST["fine"]);
            $stmt->bindParam(":t",$_POST["tipo"]);
            $stmt->execute();
            echo "OK";
        }
        else{
            echo "Non autorizzato";
        }
    default:
        # code...
        break;
}