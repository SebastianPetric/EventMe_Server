<?php 

$response= array();

if(isset($_POST['user_id'])&&isset($_POST['event_id'])){

$user_id=$_POST['user_id'];
$event_id=$_POST['event_id'];
$task_inactive=-1;

require_once 'db_connect.php';
$db = new DB_CONNECT();

$removeFriendFromEvent= mysql_query("DELETE FROM event_user WHERE user_id='$user_id' AND event_id='$event_id'");

if($removeFriendFromEvent){
    $updateTasks= mysql_query("UPDATE task SET editor_id='$task_inactive' WHERE editor_id='$user_id' AND event_id='$event_id'");
	if($updateTasks){
		$response["status"] = 200;
    	$response["message"] = "User entfernt und Tasks freigegeben.";
    	echo json_encode($response);
	}else{
 		$response["status"] = 400;
   		$response["message"] = "Oops. Da ist ein Fehler aufgetreten!";
   		echo json_encode($response);
	}
}else{
   $response["status"] = 400;
   $response["message"] = "Oops. Da ist ein Fehler aufgetreten!";
   echo json_encode($response);
}
}
?>