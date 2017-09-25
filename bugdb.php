<html>

<head>
    <title>Bugs OIS Alberghetti</title>
    <script type="text/javascript" src="res/js/main.js"></script>
    <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="res/css/stile.css">
    <link rel="stylesheet" href="res/css/awesomplete.css" />
    <script src="res/js/awesomplete.min.js" async></script>
    <script type="text/javascript">
        var a;

        function loadSearch() {
            a = new Awesomplete(document.querySelector("#q"));
            var i = document.querySelector("#q");
            //theSearch();
        }

        function thesearch() {
            var ajax = new XMLHttpRequest();
            ajax.open("GET", "res/ac.php?term=" + document.getElementById("q").value, true);
            ajax.onload = function () {
                var list = JSON.parse(ajax.responseText);
                a.list = list;
            };
            ajax.send();
        }
    </script>
</head>

<body onload="load();loadSearch()">
    <h1>
        Bug Database
    </h1>
    <div class="leftPart">
    </div>
    <div class="mainPart">
        <a href="aggiungibug.html" class="button">+ Segnala un bug</a>
        <div class="post">
            <div class="aperti">
                <h2 class="mnw">
                    Bug aperti
                </h2>
                <div class="multicols">
                    <?php
    $file_db = new PDO('sqlite:res/bugs.sqlite');
    $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if(isset($_POST['action'])){
        switch ($_POST['action']) {
            case 'segnala':
                $lang="cpp";
                if(strpos($_POST["tags"],"python")!==false){
                    $lang="python";
                }
                $id=date("Y")."-".strval(intval(file_get_contents(date("Y")))+1);
                $data = date("Y-m-d H:i:s");
                $qry="INSERT INTO Bugs VALUES (:id, :affects, :notify, :code, :lang, :creator, \"0\", :data, :comment, :descr, :tags)";
                $stmt = $file_db->prepare($qry);
                $stmt->bindParam(':id',$id);
                $stmt->bindParam(':affects',$_POST["affects"]);
                $stmt->bindParam(':notify',$_POST["notify"]);
                $stmt->bindParam(':code',$_POST["code"]);
                $stmt->bindParam(':creator',$_POST["creator"]);
                $stmt->bindParam(':lang',$lang);
                $stmt->bindParam(':comment',$_POST["commento"]);
                $stmt->bindParam(':descr',$_POST["desc"]);
                $stmt->bindParam(':tags',$_POST["tags"]);
                $stmt->bindParam(':data',$data);
                $stmt->execute();
                exec("python slack.py ".$id);
                //var_dump($_POST);
                file_put_contents(date("Y"), strval(intval(file_get_contents(date("Y")))+1));
                break;
            default:
                # code...
                break;
        }
    }
    $qry="SELECT * FROM Bugs WHERE fixed = 0";
    $stmt = $file_db->prepare($qry);
    $stmt->execute();
    $nonRisolti=$stmt->fetchAll(PDO::FETCH_ASSOC);
    if(count($nonRisolti)==0){
        echo "Hey! Tutti i bug sono risolti!";
    }
    echo "<ul>\n";
    foreach ($nonRisolti as $key => $value) {
        echo "<li>\n\t<a href=\"bug.php?id=".$value["ID"]."\">".mb_convert_encoding($value["desc"], 'utf-8', 'iso-8859-1')." in ".$value['affects']."</a>\n</li>\n";
    }
    ?></ul>
                </div>
            </div>
            <form method="GET" action="">
                <label>Ricerca tutti i bug riguardanti un problema</label>
                <input id="q" name="search" onkeyup="thesearch()">
                <input type="submit" value="Cerca">
            </form>
            <?php
        $file_db = new PDO('sqlite:/var/www/html/bugs/bugs.sqlite');
        $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if(isset($_GET['search'])){
            $_GET['search']=strtolower($_GET['search']);
            $qry="SELECT * FROM Bugs WHERE affects = :id";
            $stmt = $file_db->prepare($qry);
            $stmt->bindParam(':id',$_GET['search']);
            $stmt->execute();
            $nonRisolti=$stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<div class=\"ricerca\"><h2>Bug riguardanti ".$_GET['search']."</h2>\n<ul>\n";
            foreach ($nonRisolti as $key => $value) {
                echo "<li>\n\t<a href=\"bug.php?id=".$value["ID"]."\">".mb_convert_encoding($value["desc"], 'utf-8', 'iso-8859-1')." in ".$value['affects']."</a>\n</li>\n";
            }
        }
        echo "</ul></div>\n";
        ?>
        </div>
    </div>
</body>

</html>