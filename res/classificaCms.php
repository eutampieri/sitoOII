<?php
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