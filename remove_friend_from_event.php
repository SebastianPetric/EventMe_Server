<?php 

$response= array();

if(isset($_POST['user_id'])&&isset($_POST['event_id'])){

$user_id=$_POST['user_id'];
$event_id=$_POST['event_id'];

require_once 'db_connect.php';
$db = new DB_CONNECT();

$addFriendToEvent= mysql_query("DELETE FROM event_user WHERE user_id='$user_id' AND event_id='$event_id'");

if($addFriendToEvent){
    $response["status"] = 200;
    $response["message"] = "User erfolgreich vom Event entfernt!";
    echo json_encode($response);
}else{
   $response["status"] = 400;
   $response["message"] = "Oops. Da ist ein Fehler aufgetreten!";
   echo json_encode($response);
}
}
?>