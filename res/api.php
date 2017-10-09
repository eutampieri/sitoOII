<?php
function getSymLink($fn,$bd){
	$fn=$fn.'.svg';
	if(is_file($bd.'/'.$fn)){
		$cntt=file_get_contents($bd.'/'.$fn);
		//error_log($bd.'/'.$cntt);
		if(is_file($bd.'/'.$cntt)){
			return $bd.'/'.$cntt;
		}
		return $bd.'/'.$fn;
	}
	else{
		return $bd.'/application-octet-stream.svg';
	}
}
if(!is_file("db.sqlite")&&isset($_GET["nodb"])){
    echo "1";
    die();
}
if(is_file("db.sqlite")){
    $database = new PDO("sqlite:db.sqlite");
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    
}
else{
    $database=null;
}
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
        $stmt=$database->prepare("SELECT Username FROM Sessioni WHERE ID = :id AND Username IN (SELECT Utenti.Username FROM Tutor INNER JOIN Utenti ON Tutor.CMSUser = Utenti.CMSUser)");
        $stmt->bindParam(":id", $_COOKIE["sessione"]);
        $stmt->execute();
        echo strval(count($stmt->fetchAll(PDO::FETCH_ASSOC)));
        break;
    case "addEvent":
        $stmt=$database->prepare("SELECT Username FROM Sessioni WHERE ID = :id AND Username IN (SELECT Utenti.Username FROM Tutor INNER JOIN Utenti ON Tutor.CMSUser = Utenti.CMSUser)");
        $stmt->bindParam(":id", $_COOKIE["sessione"]);
        $stmt->execute();
        if(count($stmt->fetchAll(PDO::FETCH_ASSOC))==1){
            $stmt=$database->prepare("INSERT INTO Eventi VALUES (:d , :i , :f , :t )");
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
        break;
    case "caricaFile":
        $stmt=$database->prepare("SELECT Username FROM Sessioni WHERE ID = :id AND Username IN (SELECT Utenti.Username FROM Tutor INNER JOIN Utenti ON Tutor.CMSUser = Utenti.CMSUser)");
        $stmt->bindParam(":id", $_COOKIE["sessione"]);
        $stmt->execute();
        if(count($stmt->fetchAll(PDO::FETCH_ASSOC))==1){
            $stmt=$database->prepare("SELECT Username FROM Sessioni WHERE ID = :id");
            $stmt->bindParam(":id", $_COOKIE["sessione"]);
            $stmt->execute();
            $autore=$stmt->fetchAll(PDO::FETCH_ASSOC)[0]["Username"];
            $data=time();
            $file=file_get_contents($_FILES["file"]["tmp_name"]);
            $stmt=$database->prepare("INSERT INTO Risorse VALUES (:nome , :file , :autore , :data)");
            $stmt->bindParam(":nome",$_POST["nome"]);
            $stmt->bindParam(":file",$file);
            $stmt->bindParam(":autore",$autore);
            $stmt->bindParam(":data",$data);
            $stmt->execute();
            header("Location:../admin/file.html#ok?File%20caricato%20correttamente");
        }
        else{
            header("Location:../admin/file.html#err?Non%20autorizzato");
        }
        break;
    case "cercaFile":
        $stmt=$database->prepare("SELECT Nome, Data FROM Risorse WHERE Nome LIKE :q");
        $_GET["q"]='%'.$_GET["q"].'%';
        $stmt->bindParam(":q", $_GET["q"]);
        $stmt->execute();
        $dati=$stmt->fetchAll(PDO::FETCH_ASSOC);
        $res=[];
        for($i=0;$i<count($dati);$i++){
            array_push($res,[$dati[$i]["Nome"], $dati[$i]["Data"]]);
        }
        echo json_encode($res);
        break;
    case "iconaFile":
        $stmt=$database->prepare("SELECT File FROM Risorse WHERE Data = :id");
        $_GET["id"]=intval($_GET["id"]);
        $stmt->bindParam(":id", $_GET["id"]);
        $stmt->execute();
        $mime=str_replace('/','-',finfo_buffer(finfo_open(FILEINFO_MIME_TYPE),$stmt->fetchAll(PDO::FETCH_ASSOC)[0]["File"]));
        header("Location: ".getSymLink($mime,"icons/files"));
        break;
    default:
        # code...
        break;
}