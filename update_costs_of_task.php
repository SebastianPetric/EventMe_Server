<?php 

$response=array();

if(isset($_POST['task_id'])&&isset($_POST['editor_id'])&&isset($_POST['costs_of_task'])&&isset($_POST['type_of_update'])){

$task_id= $_POST['task_id'];
$editor_id=$_POST['editor_id'];
$costs_of_task=round(($_POST['costs_of_task']),2);
$type_of_update=$_POST['type_of_update'];
$status_inactive=-1;

require_once 'db_connect.php';
$db = new DB_CONNECT();

$checkIfTaskNotLocked= mysql_query("SELECT * FROM task WHERE task_id='$task_id' AND editor_id='$status_inactive'");
$checkIfTaskAlreadyLocked= mysql_query("SELECT * FROM task WHERE task_id='$task_id' AND editor_id='$editor_id'");

if(mysql_num_rows($checkIfTaskNotLocked)>0){
		$response["status"] = 400;
        $response["message"] = "Du musst dich erst für diese Aufgabe locken!";  
        echo json_encode($response);
}else if(mysql_num_rows($checkIfTaskAlreadyLocked)>0){
		if(intval($type_of_update)==0){
		$result= (mysql_query("UPDATE task SET cost='$costs_of_task' WHERE task_id='$task_id'"));
		
	}else if(intval($type_of_update)==1){
		$getCosts= mysql_query("SELECT * FROM task WHERE task_id='$task_id' AND editor_id='$editor_id'");	
		$costs=mysql_fetch_array($getCosts);
		$total= round(($costs['cost']+$costs_of_task),2);
		$result= mysql_query("UPDATE task SET cost='$total' WHERE task_id='$task_id'");	
	}
		if($result){
		$response["status"] = 200;
        $response["message"] = "Kosten erfolgreich aktualisiert!";  
        echo json_encode($response);
		}else{
		$response["status"] = 400;
        $response["message"] = "Oops! Versuch es später noch einmal";  
        echo json_encode($response);
		}
}else{
		$response["status"] = 400;
        $response["message"] = "Jemand anderes bearbeitet diese Aufgabe bereits!";  
        echo json_encode($response);
}
}else{
		$response["status"] = 400;
        $response["message"] = "Oops! Versuch es später noch einmal";  
        echo json_encode($response);
}
?>