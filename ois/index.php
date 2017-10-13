<?php
header("Content-Type: application/json");
$jsonBody=file_get_contents("php://input");
$update = json_decode($jsonBody, true);
//error_log("k-".$jsonBody);
$parameters=array();
//error_log("Connessione\n");
$msg=uniqid();
file_put_contents($msg,$update["message"]["text"]);
if($update["message"]["text"]=="/start"){
	$parameters["method"]="sendMessage";
	$parameters["text"]="Benvenuto! Scrivi il nome del problema.";
	$parameters["chat_id"]=$update["message"]["from"]["id"];
	echo json_encode($parameters);
}
elseif(strpos($update["message"]["text"], '/dl') !== false){
	exec("python dl.py '".$msg."' ".$update["message"]["from"]["id"]);
}
else{
	exec("python fetch.py ".$msg." ".$update["message"]["from"]["id"]);
}
//error_log("Msg Inviato\n");
?>
