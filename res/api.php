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
if($azione=="ical"){
    if(!is_file("ical.php")){
        file_put_contents("ical.ver",file_get_contents("https://raw.githubusercontent.com/eutampieri/PHPiCal/master/VERSION"));
        file_put_contents("ical.php",file_get_contents("https://raw.githubusercontent.com/eutampieri/PHPiCal/master/ical.php"));
    }
    elseif(intval(file_get_contents("ical.ver"))<intval(file_get_contents("https://raw.githubusercontent.com/eutampieri/PHPiCal/master/VERSION"))){
        file_put_contents("ical.ver",file_get_contents("https://raw.githubusercontent.com/eutampieri/PHPiCal/master/VERSION"));
        file_put_contents("ical.php",file_get_contents("https://raw.githubusercontent.com/eutampieri/PHPiCal/master/ical.php"));
    }
    require("ical.php");
}
if($azione=="feed"){
    if(!is_file("phParticle.php")){
        file_put_contents("feed.ver",file_get_contents("https://raw.githubusercontent.com/eutampieri/phParticle/master/VERSION"));
        file_put_contents("phParticle.php",file_get_contents("https://raw.githubusercontent.com/eutampieri/phParticle/master/phParticle.php"));
    }
    elseif(intval(file_get_contents("feed.ver"))<intval(file_get_contents("https://raw.githubusercontent.com/eutampieri/phParticle/master/VERSION"))){
        file_put_contents("feed.ver",file_get_contents("https://raw.githubusercontent.com/eutampieri/phParticle/master/VERSION"));
        file_put_contents("phParticle.php",file_get_contents("https://raw.githubusercontent.com/eutampieri/phParticle/master/phParticle.php"));
    }
    file_put_contents("parsedown.php",file_get_contents("https://raw.githubusercontent.com/erusev/parsedown/master/Parsedown.php"));
    require("phParticle.php");
    require("parsedown.php");
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
        echo file_get_contents("https://training.olinfo.it/api/user", false, $context);
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
        echo file_get_contents("https://training.olinfo.it/api/user", false, $context);
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
    case "sideBar":
        $res="";
        $stmt=$database->prepare("SELECT Username FROM Sessioni WHERE ID = :id AND Username IN (SELECT Utenti.Username FROM Tutor INNER JOIN Utenti ON Tutor.CMSUser = Utenti.CMSUser)");
        $stmt->bindParam(":id", $_COOKIE["sessione"]);
        $stmt->execute();
        $stmt1=$database->prepare("SELECT Username FROM Sessioni WHERE ID = :id");
        $stmt1->bindParam(":id", $_COOKIE["sessione"]);
        $stmt1->execute();
        if(count($stmt->fetchAll(PDO::FETCH_ASSOC))==1){
            $res=$res.file_get_contents("adminMenu.html");
        }
        if(count($stmt1->fetchAll(PDO::FETCH_ASSOC))==1){
            $res=$res.file_get_contents("userMenu.html");
        }
        else{
            $res=$res.file_get_contents("regMenu.html");
        }
        $res=$res."</ul>";
        echo $res;
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
            $stmt->bindParam(":d",mb_convert_encoding($_POST["descrizione"], 'utf-8', 'iso-8859-1'));
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
    case "ical":
        $cal = new iCalendar();
        header("Content-Type: text/calendar");
        $stmt=$database->prepare("SELECT Inizio AS start, Fine as end, Descrizione as desc FROM Eventi");
        $stmt->execute();
        echo $cal->ical($stmt->fetchAll(PDO::FETCH_ASSOC));

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
            $stmt->bindParam(":nome",mb_convert_encoding($_POST["nome"], 'utf-8', 'iso-8859-1'));
            $stmt->bindParam(":file",$file);
            $stmt->bindParam(":autore",$autore);
            $stmt->bindParam(":data",$data);
            $stmt->execute();
            header("Location:../admin/file.html#ok&File%20caricato%20correttamente");
        }
        else{
            header("Location:../admin/file.html#err&Non%20autorizzato");
        }
        break;
    case "idFile":
        $stmt=$database->prepare("SELECT Data FROM Risorse WHERE Nome = :q");
        $stmt->bindParam(":q", $_GET["q"]);
        $stmt->execute();
        $dati=$stmt->fetchAll(PDO::FETCH_ASSOC);
        echo $dati[0]["Data"];
        break;
    case "cercaFile":
        $stmt=$database->prepare("SELECT Nome FROM Risorse WHERE Nome LIKE :q");
        $_GET["q"]='%'.$_GET["q"].'%';
        $stmt->bindParam(":q", $_GET["q"]);
        $stmt->execute();
        $dati=$stmt->fetchAll(PDO::FETCH_ASSOC);
        $res=[];
        for($i=0;$i<count($dati);$i++){
            array_push($res,mb_convert_encoding($dati[$i]["Nome"], "UTF-8"));
        }
        echo json_encode($res);
        break;
    case "iconaFile":
        $stmt=$database->prepare("SELECT File FROM Risorse WHERE Data = :id");
        $id=intval($_GET["id"]);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $mime=str_replace('/','-',finfo_buffer(finfo_open(FILEINFO_MIME_TYPE),$stmt->fetchAll(PDO::FETCH_ASSOC)[0]["File"]));
        header("Location: ".getSymLink($mime,"icons/files"));
        break;
    case "dlFile":
        $stmt=$database->prepare("SELECT File FROM Risorse WHERE Data = :id");
        $id=intval($_GET["id"]);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $file=$stmt->fetchAll(PDO::FETCH_ASSOC)[0]["File"];
        header("Content-Type: ".finfo_buffer(finfo_open(FILEINFO_MIME_TYPE),$file));
        echo $file;
        break;
    case "creaPost":
        $stmt=$database->prepare("SELECT Username FROM Sessioni WHERE ID = :id AND Username IN (SELECT Utenti.Username FROM Tutor INNER JOIN Utenti ON Tutor.CMSUser = Utenti.CMSUser)");
        $stmt->bindParam(":id", $_COOKIE["sessione"]);
        $stmt->execute();
        if(count($stmt->fetchAll(PDO::FETCH_ASSOC))==1){
            $stmt=$database->prepare("SELECT Username FROM Sessioni WHERE ID = :id");
            $stmt->bindParam(":id", $_COOKIE["sessione"]);
            $stmt->execute();
            $autore=$stmt->fetchAll(PDO::FETCH_ASSOC)[0]["Username"];
            $data=time();
            $stmt=$database->prepare("INSERT INTO Post VALUES (:titolo , :contenuto , :data , :autore)");
            $stmt->bindParam(":titolo", $_POST["titolo"]);
            $stmt->bindParam(":contenuto", mb_convert_encoding($_POST["contenuto"], 'utf-8', 'iso-8859-1'));
            $stmt->bindParam(":data", $data);
            $stmt->bindParam(":autore", $autore);
            $stmt->execute();
            echo "OK";
        }
        else{
            echo "Non autorizzato";
        }
        break;
    case "postList":
        $stmt=$database->prepare('SELECT Titolo, Contenuto, Data, Nome ||" "|| Cognome AS Autore FROM Post INNER JOIN Utenti ON Post.Autore=Utenti.Username ORDER BY Data DESC;');
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
    case "post":
        $stmt=$database->prepare('SELECT Titolo, Contenuto, Data, Nome ||" "|| Cognome AS Autore FROM Post INNER JOIN Utenti ON Post.Autore=Utenti.Username WHERE Data = :id');
        $stmt->bindParam(":id", $_GET["id"]);
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC)[0]);
        break;
    case "task":
        $data = array('action' => "get", 'name' =>$_GET["task"]);
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($data)
            )
        );
        $context  = stream_context_create($options);
        echo file_get_contents("https://training.olinfo.it/api/task", false, $context);
        break;
    case "userCMS":
        $data = array('action' => "get", 'username' =>$_GET["user"]);
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($data)
            )
        );
        $context  = stream_context_create($options);
        echo file_get_contents("https://training.olinfo.it/api/user", false, $context);
        break;
    case "listaUtentiCMS":
        $stmt=$database->prepare("SELECT Nome, Cognome, Classe, CMSUser FROM Utenti");
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
    case "rifClassifica":
        $stmt=$database->prepare("SELECT * FROM RifClassifica");
        $stmt->execute();
        $a=$stmt->fetchAll(PDO::FETCH_ASSOC);
        $u=[];
        foreach($a as $b){
            array_push($u,$b["CMSUser"]);
        }
        echo json_encode($u);
        break;
    case "feed":
        header("Content-Type: application/atom+xml");
        $stmt=$database->prepare('SELECT Titolo as title, Contenuto as content, Data as date, Data as url, Nome ||" "|| Cognome AS author FROM Post INNER JOIN Utenti ON Post.Autore=Utenti.Username ORDER BY date DESC;');
        $stmt->execute();
        $feed=new phParticle("Olimpiadi di Informatica",function($id,$url){
            $url=str_replace("res/",'',$url);
            $url=$url."post.html#".strval($id);
            return $url;
        }, function($md){
            $p = new Parsedown();
            return $p->text($md);
        });
        echo $feed->feed($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
    case "userDetailByCMS":
        $stmt=$database->prepare('SELECT Nome as nome, Cognome as cognome, Classe as classe FROM Utenti WHERE CMSUser = :u;');
        $stmt->bindParam(":u", $_GET["user"]);
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC)[0]);
        break;
    case "thisUserInfo":
        $stmt=$database->prepare("SELECT Utenti.Nome as nome, Utenti.Cognome as cognome, Utenti.Classe as classe, Utenti.Username as user, Utenti.CMSUser as cms FROM Sessioni INNER JOIN Utenti ON Sessioni.Username = Utenti.Username WHERE ID = :id");
        $stmt->bindParam(":id",$_COOKIE["sessione"]);
        $stmt->execute();
        $res=$stmt->fetchAll(PDO::FETCH_ASSOC)[0];
        if(count($res)==0){
            echo "{}";
            break;
        }
        $res=json_decode(json_encode($res),true);
        $stmt=$database->prepare("SELECT Username FROM Sessioni WHERE ID = :id AND Username IN (SELECT Utenti.Username FROM Tutor INNER JOIN Utenti ON Tutor.CMSUser = Utenti.CMSUser)");
        $stmt->bindParam(":id", $_COOKIE["sessione"]);
        $stmt->execute();
        $res["isTutor"]=(count($stmt->fetchAll(PDO::FETCH_ASSOC))==1);
        echo json_encode($res);
        break;
    case "eliminaAccount":
        $stmt=$database->prepare("SELECT Utenti.Username as user, Utenti.Password as Password FROM Sessioni INNER JOIN Utenti ON Sessioni.Username = Utenti.Username WHERE ID = :id");
        $stmt->bindParam(":id",$_COOKIE["sessione"]);
        $stmt->execute();
        $utenti=$stmt->fetchAll(PDO::FETCH_ASSOC);
        if(count($utenti)==1){
            if(password_verify($_POST["password"],$utenti[0]["Password"])){
                $stmt=$database->prepare("DELETE FROM Utenti WHERE Username = :u");
                $stmt->bindParam(":u",$utenti[0]["user"]);
                $stmt->execute();
                $stmt=$database->prepare("DELETE FROM Sessioni WHERE ID = :id");
                $stmt->bindParam(":id",$_COOKIE["sessione"]);
                $stmt->execute();
                setcookie("sessione",null,time()-100);
                echo "OK";
            }
            else{
                echo "Password errata";
            }
        }
        break;
        case "logout":
            $stmt=$database->prepare("DELETE FROM Sessioni WHERE ID = :id");
            $stmt->bindParam(":id",$_COOKIE["sessione"]);
            $stmt->execute();
            setcookie("sessione",null,time()-100);
            header("Location: ../index.html#ok&Logout%20avvenuto%20con%20successo");
    default:
        # code...
        break;
}