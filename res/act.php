<?php
$url = 'https://training.olinfo.it/api/task';
$data = array("action"=>"list","search"=>$_GET["problema"],"first"=>0,"last"=>100);

// use key 'http' even if you send the request to https://...
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
$prbls=["altro","errori stupidi"];
$file_db = new PDO('sqlite:bugs.sqlite');
$file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$qry="SELECT tags FROM Bugs";
$stmt = $file_db->prepare($qry);
$stmt->execute();
$tags=$stmt->fetchAll(PDO::FETCH_ASSOC);
for($i=0;$i<count($tags); $i++) {
	if($tags[$i]["tags"]!=NULL){
		array_push($prbls, $tags[$i]["tags"]);
	}
}

for($i=0;$i<count($result["tasks"]);$i++){
	$qdata = array("action"=>"get","name"=>$result["tasks"][$i]["name"]);
	$qoptions = array(
	    'http' => array(
	        'header'  => "Content-type: application/json\r\n",
	        'method'  => 'POST',
	        'content' => json_encode($qdata)
	    )
	);
	$qcontext  = stream_context_create($qoptions);
	$qresult = json_decode(file_get_contents($url, false, $qcontext),true);
	for($j=0;$j<count($qresult["tags"]);$j++){
		array_push($prbls, $qresult["tags"][$j]["name"]);
	}
}
$url = 'https://aica.cms.di.unipi.it/api/task';
$data = array("action"=>"list","search"=>$_GET["problema"],"first"=>0,"last"=>100);

// use key 'http' even if you send the request to https://...
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
	$qdata = array("action"=>"get","name"=>$result["tasks"][$i]["name"]);
	$qoptions = array(
	    'http' => array(
	        'header'  => "Content-type: application/json\r\n",
	        'method'  => 'POST',
	        'content' => json_encode($qdata)
	    )
	);
	$qcontext  = stream_context_create($qoptions);
	$qresult = json_decode(file_get_contents($url, false, $qcontext),true);
	for($j=0;$j<count($qresult["tags"]);$j++){
		array_push($prbls, $qresult["tags"][$j]["name"]);
	}
}
echo json_encode($prbls);
//echo exec("python afetch.py ".$_GET["term"]);