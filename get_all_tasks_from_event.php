<?php 
$response=array();

if(isset($_POST['event_id'])){

$event_id = $_POST['event_id'];

require_once 'db_connect.php';

$db = new DB_CONNECT();

$result= mysql_query("SELECT * FROM task WHERE event_id='$event_id'");

if(mysql_num_rows($result)>0){

$response["tasks"] = array();
$task=array();

while ($row = mysql_fetch_array($result)) {

$task["task_id"]=$row["task_id"];
$task["event_id"]=$row["event_id"];
$task["task"]=$row["task"];
$task["quantity"]=$row["quantity"];
$task["description"]=$row["description"];
$task["costs_of_task"]=$row["cost"];
$task["percentage_of_task"]=$row["percentage"];

$editor_id=$row["editor_id"];
$getEditor= mysql_query("SELECT name FROM user WHERE user_id='$editor_id'");

if(mysql_num_rows($getEditor)>0){
	$editor_name_array=mysql_fetch_array($getEditor);
	$task["editor_name"]=$editor_name_array["name"];
}else{
	$task["editor_name"]="offen";
}
$task["editor_id"]=$row["editor_id"];
array_push($response["tasks"], $task);
}
 $response["status"]=200;
 $response["message"]="Aufgaben aktualisiert.";
 echo json_encode($response);



}else{
 $response["status"]=400;
 $response["message"]="Keine Aufgaben für dieses Event vorhanden.";
 echo json_encode($response);
}

}else{
 $response["status"]=400;
 $response["message"]="Fehler. Versuchen Sie es später noch ein Mal!";
 echo json_encode($response);
}

?>