<?php
$url = 'https://training.olinfo.it/api/task';
$data = array("action"=>"list","search"=>$_GET["term"],"first"=>0,"last"=>100);
$options = array(
    'http' => array(
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data)
    )
);
$context  = stream_context_create($options);
$result = json_decode(file_get_contents($url, false, $context),true);
if ($result === FALSE) { /* Handle error */ }
$prbls=["altro"];
$file_db = new PDO('sqlite:bugs.sqlite');
$file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$qry="SELECT affects FROM Bugs WHERE affects like :a";
$stmt = $file_db->prepare($qry);
$_GET["term"]='%'.$_GET["term"].'%';
$stmt->bindParam(":a",$_GET["term"]);
$stmt->execute();
$tags=$stmt->fetchAll(PDO::FETCH_ASSOC);
for($i=0;$i<count($tags); $i++) {
	if($tags[$i]["affects"]!=NULL&&!in_array($tags[$i]["affects"], $prbls)){
		array_push($prbls, $tags[$i]["affects"]);
	}
}
for($i=0;$i<count($result["tasks"]);$i++){
	if(!in_array($result["tasks"][$i]["name"], $prbls))
	array_push($prbls, $result["tasks"][$i]["name"]);
}
$url = 'https://aica.cms.di.unipi.it/api/task';
$data = array("action"=>"list","search"=>$_GET["term"],"first"=>0,"last"=>100);
$options = array(
    'http' => array(
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data)
    )
);
$context  = stream_context_create($options);
$result = json_decode(file_get_contents($url, false, $context),true);
for($i=0;$i<count($result["tasks"]);$i++){
	if(!in_array($result["tasks"][$i]["name"], $prbls))
	array_push($prbls, $result["tasks"][$i]["name"]);
}
echo json_encode($prbls);