<?php 


$response= array();

if(isset($_POST['usera_id'])&&isset($_POST['userb_id'])){

$usera_id=$_POST['usera_id'];
$userb_id=$_POST['userb_id'];

require_once 'db_connect.php';

$db = new DB_CONNECT();

	$result=mysql_query("DELETE FROM friends WHERE ((user_a,user_b)= ('$usera_id','$userb_id') OR (user_a,user_b)= ('$userb_id','$usera_id'))");
	if($result){
	$response["status"]=200;
	$response["message"]="Freund gelöscht.";
	echo json_encode($response);
	}else{
	$response["status"]=400;
	$response["message"]="Oops! Versuch es später noch einmal";
	echo json_encode($response);
	}

}else{
	$response["status"]=400;
	$response["message"]="Oops! Versuch es später noch einmal";
	echo json_encode($response);
	}

?>