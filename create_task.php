<?php 

$response= array();

if(isset($_POST['event_id'])&&isset($_POST['editor_id'])&&isset($_POST['task'])&&isset($_POST['description'])&&isset($_POST['quantity'])){


require_once 'db_connect.php';
$db = new DB_CONNECT();

$event_id = $_POST['event_id'];
$editor_id = $_POST['editor_id'];
$task = mysql_real_escape_string($_POST['task']);
$description = mysql_real_escape_string($_POST['description']);
$quantity = mysql_real_escape_string($_POST['quantity']);

$result= mysql_query("INSERT INTO task (event_id,task,editor_id,description,quantity) VALUES ('$event_id','$task','$editor_id','$description','$quantity')");

if($result){
	$response["status"]=200;
	$response["message"]="Aufgabe erstellt.";
	echo json_encode($response);
}else{
	$response["status"]=400;
	$response["message"]="Fehler beim erstellen der Aufgabe. Versuchen Sie es später noch ein Mal!";
	echo json_encode($response);
}
}else{
	$response["status"]=400;
	$response["message"]="Felder nicht korrekt ausgefüllt!";
	echo json_encode($response);
}
?>