<?php 

$response=array();

if(isset($_POST['task_id'])&&isset($_POST['costs_of_task'])){

$task_id= $_POST['task_id'];
$costs_of_task=$_POST['costs_of_task'];

require_once 'db_connect.php';

$db = new DB_CONNECT();

$result= mysql_query("UPDATE task SET cost='$costs_of_task' WHERE task_id='$task_id'");

if($result){
		$response["status"] = 200;
        $response["message"] = "Kosten erfolgreich aktualisiert";  
        echo json_encode($response);
}else{
		$response["status"] = 400;
        $response["message"] = "Oops! Versuch es später noch einmal";  
        echo json_encode($response);
}

}else{
		$response["status"] = 400;
        $response["message"] = "Oops! Versuch es später noch einmal";  
        echo json_encode($response);
}
?>