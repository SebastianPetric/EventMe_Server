<?php 

$response= array();

if(isset($_POST['user_id'])&&isset($_POST['event_id'])){

$user_id=$_POST['user_id'];
$event_id=$_POST['event_id'];

require_once 'db_connect.php';

if($add_friend_to_event = $db->prepare("INSERT INTO event_user (event_id,user_id) VALUES (:event_id,:user_id)")){
				$db->beginTransaction();
				$add_friend_to_event->bindParam(':event_id', $event_id);
                $add_friend_to_event->bindParam(':user_id', $user_id);
                $add_friend_to_event->execute();
                if($add_friend_to_event){
                	$db -> commit ();
    				$response["status"] = 200;
    				$response["message"] = "User erfolgreich zum Event hinzugefügt!";
    				echo json_encode($response);
				}else{
					$db -> rollBack ();
   					$response["status"] = 400;
   					$response["message"] = "Oops. Da ist ein Fehler aufgetreten!";
   					echo json_encode($response);
				}
}else{
	 $response["status"] = 400;
     $response["message"] = "Oops. Es ist ein Fehler aufgetreten!";
     echo json_encode($response);
}
$db = null;  
}else{
	$response["status"]=400;
	$response["message"]="Es wurden nicht alle Datensätze übertragen!";
	echo json_encode($response);
}
?>