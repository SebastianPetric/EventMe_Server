<?php 

$response=array();

if(isset($_POST['task_id'])&&isset($_POST['editor_id'])&&isset($_POST['percentage_of_task'])){

$task_id= $_POST['task_id'];
$editor_id=$_POST['editor_id'];
$percentage=$_POST['percentage_of_task'];
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

		$result= mysql_query("UPDATE task SET percentage='$percentage' WHERE task_id='$task_id'");
		
		if($result){
		$response["status"] = 200;
        $response["message"] = "Status erfolgreich aktualisiert!";  
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