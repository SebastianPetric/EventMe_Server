<?php 


$response= array();

if(isset($_POST['usera_id'])&&isset($_POST['userb_id'])){

$usera_id=$_POST['usera_id'];
$userb_id=$_POST['userb_id'];
$status_open=0;
$status_friended=2;

require_once 'db_connect.php';

$db = new DB_CONNECT();

$checkIfDeleteFriend= mysql_query("SELECT * FROM friends WHERE ((user_a,user_b)= ('$usera_id','$userb_id') OR (user_a,user_b)= ('$userb_id','$usera_id')) AND status='$status_friended'");
$checkIfDenyRequest= mysql_query("SELECT * FROM friends WHERE (user_b,user_a)= ('$usera_id','$userb_id') AND status='$status_open'");

if(mysql_num_rows($checkIfDeleteFriend)>0){
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
}else if(mysql_num_rows($checkIfDenyRequest)>0){
	$result=mysql_query("DELETE FROM friends WHERE ((user_a,user_b)= ('$usera_id','$userb_id') OR (user_a,user_b)= ('$userb_id','$usera_id'))");
	if($result){
	$response["status"]=200;
	$response["message"]="Freundschaftsanfrage abgelehnt.";
	echo json_encode($response);
	}else{
	$response["status"]=400;
	$response["message"]="Oops! Versuch es später noch einmal";
	echo json_encode($response);
	}
}
}else{
	$response["status"]=400;
	$response["message"]="Oops! Versuch es später noch einmal";
	echo json_encode($response);
	}
?>