<?php 

$response= array();

if(isset($_POST['user_id'])&&isset($_POST['event_id'])){

$user_id=$_POST['user_id'];
$event_id=$_POST['event_id'];

require_once 'db_connect.php';
$db = new DB_CONNECT();

$addFriendToEvent= mysql_query("INSERT INTO event_user (event_id,user_id) VALUES ('$event_id','$user_id')");

if($addFriendToEvent){
    $response["status"] = 200;
    $response["message"] = "User erfolgreich zum Event hinzugefügt!";
    echo json_encode($response);
}else{
   $response["status"] = 400;
   $response["message"] = "Oops. Da ist ein Fehler aufgetreten!";
   echo json_encode($response);
}
}
?>