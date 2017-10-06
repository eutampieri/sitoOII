<?php
if(is_file("res/db.sqlite")){
    $database = new PDO("sqlite:res/db.sqlite");
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    
}
else{
    $database=null;
}
if(isset($_POST["username"])&&isset($_POST["password"])){
    $stmt=$database->prepare("SELECT Password FROM Utenti WHERE Username = :u");
    $stmt->bindParam(":u",$_POST["username"]);
    $stmt->execute();
    $utenti=$stmt->fetchAll(PDO::FETCH_ASSOC);
    if(count($utenti)==1){
        if(password_verify($_POST["password"],$utenti[0]["Password"])){
            header("Location: index.html#ok&Login%20avvenuto%20con%20successo");
            $sessID=uniqid("sess");
            setcookie("sessione",$sessID);
            $qry="INSERT INTO Sessioni VALUES(:id , :user)";
            $stmt=$database->prepare($qry);            
            $stmt->bindParam(':id', $sessID);
            $stmt->bindParam(':user', $_POST["username"]);
            $stmt->execute();
        }
        else{
            header("Location: login.html#err&Password%20errata");
        }
    }
    else{
        header("Location: login.html#err&Utente%20inesistente");
    }
}
else{
    header("Location: login.html#err&Compila%20il%20modulo");
}