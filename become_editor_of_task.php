<?php 

$response=array();

if(isset($_POST['task_id'])&&isset($_POST['editor_id'])){

$task_id= $_POST['task_id'];
$editor_id=$_POST['editor_id'];
$status_inactive=-1;

require_once 'db_connect.php';

$db = new DB_CONNECT();

$checkIfTaskIsInactiv= mysql_query("SELECT * FROM task WHERE task_id='$task_id' AND editor_id='$status_inactive'");

if(mysql_num_rows($checkIfTaskIsInactiv)>0){
$result= mysql_query("UPDATE task SET editor_id='$editor_id' WHERE task_id='$task_id'");
$getEditor= mysql_query("SELECT name FROM user WHERE user_id='$editor_id'");
$getEditorArray=mysql_fetch_array($getEditor);
$getEditorName=$getEditorArray["name"];
if($result){
		$response["status"] = 200;
        $response["message"] = "Sie sind der neue Bearbeiter der Aufgabe!";
        $response["editor_name"]= $getEditorName;  
        echo json_encode($response);
}else{
		$response["status"] = 400;
        $response["message"] = "Oops! Versuch es später noch einmal";  
        echo json_encode($response);
}
}else{
		$response["status"] = 400;
        $response["message"] = "Die Aufgabe wird schon bearbeitet!";  
        echo json_encode($response);
}
}else{
		$response["status"] = 400;
        $response["message"] = "Oops! Versuch es später noch einmal";  
        echo json_encode($response);
}
?>