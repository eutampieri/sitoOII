<html>
    <head>
    <title><?php echo $_GET["db"];?>.sqlite</title>
    <style>
table{
    border-collapse: collapse;
}
td, th{
    border: 1px solid black;
}
th{
    background-color: #f0f0f0;
}
</style>
</head>
<body>
<?php
$enabled=true;
if(!$enabled){
    die();
}
echo '<h1>Dati in '.$_GET["db"].'.sqlite'."</h1>\n";
$database = new PDO("sqlite:".str_replace(".",'', ('/','',$_GET["db"])).'.sqlite');
$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$qry="SELECT name FROM sqlite_master where type='table';";
$stmt=$database->prepare($qry);
$stmt->execute();
$tabelle=$stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($tabelle as $t){
    echo "<h2>".$t["name"]."</h2>\n<table><tr>";
    $qry="PRAGMA table_info(".$t["name"].");";
    $stmt=$database->prepare($qry);
    $stmt->execute();
    $colonne=$stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($colonne as $c){
        echo "<th>".$c['name']."(".$c['type'].")</th>";
    }
    echo '</tr>';
    $qry="SELECT * FROM ".$t["name"].";";
    $stmt=$database->prepare($qry);
    $stmt->execute();
    $righe=$stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($righe as $r){
        echo "<tr>";
        foreach($r as $n=>$campi){
            echo "<td>".$campi."</td>\n";
        }
        echo "</tr>";
    }
    echo "</table>\n";
}
?>
</body>
</html>