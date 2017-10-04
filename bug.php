<?php
function server($mail){
	if($mail==""){
		return false;
	}
	$m=explode("@", $mail);
	$host=$m[1];
	/*if($host=="etcloud.ddns.net"||$host=="serverseutampieri.ddns.net"){
		return array('server' => 'etcloud.ddns.net', 'porta' => 465,'user'=>'etnotify','da'=>'etnotify@etcloud.ddns.net');
	}*/
return array('server' => 'smtp-mail.outlook.com', 'porta' => 587,'user'=>'YourPreetyEmail','da'=>'no-reply.etsoftware@outlook.it');
}
include("res/class.phpmailer.php");
include("res/class.smtp.php");
$file_db = new PDO('sqlite:res/bugs.sqlite');
$file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
if(isset($_POST["action"])&&$_POST["action"]=="risolvi"){
	$id=$_GET["id"];
	$uid=strval(uniqid());
	$data = date("Y-m-d H:i:s");
	$qry="INSERT INTO Fixes VALUES (:uid, :id, :fixer, :data, \"0\", :code, :comment)";
	$stmt = $file_db->prepare($qry);
	$stmt->bindParam(':id',$id);
	$stmt->bindParam(':uid',$uid);
	$stmt->bindParam(':code',$_POST["code"]);
	$stmt->bindParam(':fixer',$_POST["fixer"]);
	$stmt->bindParam(':comment',$_POST["commento"]);
	$stmt->bindParam(':data',$data);
	$stmt->execute();
	if($_POST["isComment"]=="off"){
		$qry="UPDATE Bugs SET fixed=\"1\" WHERE ID=:id";
		$stmt = $file_db->prepare($qry);
		$stmt->bindParam(':id',$id);
		$stmt->execute();
	}
	$qry="SELECT notify,creator from Bugs WHERE ID=:id";
	$stmt = $file_db->prepare($qry);
	$stmt->bindParam(':id',$id);
	$stmt->execute();
	$mailID=$stmt->fetchAll(PDO::FETCH_ASSOC);
	$mailNotify=$mailID[0]["notify"];
	$server=server($mailNotify);
	if($server!==false){
		$mail = new PHPMailer(true); 
		$mail->IsSMTP(); 

		try {
		  $mail->IsHTML(true);
		  $mail->SMTPDebug  = false;
		  $mail->SMTPAuth   = true;
		  $mail->SMTPSecure = "tls";
		  $mail->Host       = $server["server"];
		  $mail->Port       = $server["porta"];
		  $mail->Username   = $server["user"];
		  $mail->Password   = "YourPreetyPassword";
		  $mail->AddAddress($mailNotify, $mailID[0]["creator"]);
		  $mail->SetFrom($server["da"], 'Bugs OIS');
		//  $mail->AddAddress($_POST['email'], $_POST['nome']);
		  $mail->Subject = "Il tuo bug ha una nuova soluzione";
		  $mail->Body = "<h1>Il tuo bug ha una nuova soluzione!</h1><br><a href=\"http://serverseutampieri.ddns.net/bugs/bug.php?id=".$id."\">Clicca qui per visualizzarla</a>";
		  $mail->Send();
		} catch (phpmailerException $e) {
		  //echo $e->errorMessage(); //Pretty error messages from PHPMailer
		} catch (Exception $e) {
		  //echo $e->getMessage(); //Boring error messages from anything else!
		}
	}
}
$qry="SELECT * FROM Bugs WHERE ID = :id";
$stmt = $file_db->prepare($qry);
$stmt->bindParam(':id',$_GET["id"]);
$stmt->execute();
$bug=$stmt->fetchAll(PDO::FETCH_ASSOC);
$bug=$bug[0];
$id=$bug["ID"];
$desc=mb_convert_encoding($bug["desc"], 'utf-8', 'iso-8859-1');
$datePosted=$bug["date"];
$creator=$bug["creator"];
$tags=$bug["tags"];
$affects=$bug["affects"];
$code=$bug["code"];
$comment=$bug["comment"];
$language="\"language-".$bug["language"].'"';
$soluzioni="";
$qry="SELECT * FROM Fixes WHERE BugID = :id";
$stmt = $file_db->prepare($qry);
$stmt->bindParam(':id',$_GET["id"]);
$stmt->execute();
$fixes=$stmt->fetchAll(PDO::FETCH_ASSOC);
for($i=0;$i<count($fixes);$i++){
	$fn=strval(uniqid()).'.'.$bug["language"];
	file_put_contents($fn, $fixes[$i]["code"]);
	$handle = popen("g++ -std=c++11 ".$fn." 2>&1", "r");
	$o=str_replace($fn.":", "Line ", fgets($handle));
	fclose($handle);
	unlink($fn);
	unlink("a.out");
	if($o==""){
		$o='<img class="icons" src="res/icons/ok.svg">';
	}
	else{
		$o='<img class="icons" src="res/icons/error.svg">'.$o;
	}
	if($bug["language"]=="python"){
		$o='Non ancora implementata';
	}
	$soluzioni=$soluzioni."<hr>
			<table>
				<tr>
					<td>Risolto da:</td>
					<td>".$fixes[$i]["fixer"]."</td>
				</tr>
				<tr>
					<td>Il:</td>
					<td>".$fixes[$i]["date"]."</td>
				</tr>
				<tr>
					<td>Compilazione:</td>
					<td>".$o."</td>
			</table>
			<pre class=".$language."><code class=".$language.">".str_replace("<","&lt;",str_replace(">","&gt;",$fixes[$i]["code"]))."</code></pre>
			<pre class='comment'>".$fixes[$i]["comment"]."</pre>";
}
?>
	<html>

	<head>
		<title>Bug
			<?php echo $id;?>:
			<?php echo $desc;?>
		</title>
		<style>
			.button {
				margin-bottom: 1em!important;
			}
		</style>
		<link rel="stylesheet" type="text/css" href="res/css/stile.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<script type="text/javascript" src="res/js/prism.js"></script>
		<link rel="stylesheet" type="text/css" href="res/css/prism.css">
		<script type="text/javascript" src="res/js/main.js"></script>
	</head>

	<body onload="load();">
		<h1>Bug
			<?php echo $id;?>:
			<?php echo $desc;?>
		</h1>
		<div class="leftPart"></div>
		<div class="mainPart">
			<a href="bugdb.php" class="button">Indietro</a>
			<div class="post">
				<h2>Problema</h2>
				<table>
					<tr>
						<td>Posto da:</td>
						<td>
							<?php echo $creator;?>
						</td>
					</tr>
					<tr>
						<td>Il:</td>
						<td>
							<?php echo $datePosted;?>
						</td>
					</tr>
					<tr>
						<td>Tags:</td>
						<td>
							<?php echo $tags;?>
						</td>
					</tr>
					<tr>
						<td>Riguarda:</td>
						<td>
							<?php echo $affects;?>
						</td>
					</tr>
					<tr>
						<td>Compilazione:</td>
						<td>
							<?php
				$fn=strval(uniqid()).'.'.$bug["language"];
				file_put_contents($fn, $code);
				$handle = popen("g++ -std=c++11 ".$fn." 2>&1", "r");
				$o=str_replace($fn.":", "Line ", fgets($handle));
				fclose($handle);
				unlink($fn);
				unlink("a.out");
				if($bug["language"]=="python"){
					echo 'Non ancora implementata';
				}
				else if($o==""){
					echo '<img class="icons" src="res/icons/ok.svg">';
				}
				else{
					echo '<img class="icons" src="res/icons/error.svg">'.$o;
				}
				?></td>
					</tr>
				</table>
				<pre class=<?php echo $language;?>><code class=<?php echo $language;?>><?php echo str_replace("<","&lt;",str_replace(">","&gt;",$code));?></code></pre>
				<pre class="comment"><?php echo $comment;?></pre>
				<h2>Soluzioni</h2>
				<?php echo $soluzioni;?>
				<h4>Sottoponi la tua soluzione:</h4>
				<form id="fix" method="POST" action="">
					<input type="hidden" name="action" value="risolvi">
					<label>Nome:</label><br>
					<input type="text" name="fixer"><br>
					<textarea form="fix" name="code">Inserisci qui il codice corretto</textarea><br>
					<div class="label">Descrizione</div><br>
					<textarea form="fix" name="commento">Inserisci qui una descrizione dettagliata della soluzione</textarea><br>
					<input type="checkbox" name="isComment">Questa non è la soluzione, è solo un commento<br>
					<input type="submit" value="Sottoponi">
				</form>
			</div>
		</div>
	</body>