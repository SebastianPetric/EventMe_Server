<?php 

$response= array();

if(isset($_POST['user_id'])&&isset($_POST['event_id'])){

$user_id=$_POST['user_id'];
$event_id=$_POST['event_id'];
$task_inactive=-1;

require_once 'db_connect.php';

if($remove_friend_from_event = $db->prepare("DELETE FROM event_user WHERE user_id=:user_id AND event_id=:event_id")){  
                $db->beginTransaction();
                $remove_friend_from_event->bindParam(':user_id', $user_id);
                $remove_friend_from_event->bindParam(':event_id', $event_id);
                $remove_friend_from_event->execute();
                if($remove_friend_from_event){
                  if($update_tasks = $db->prepare("UPDATE task SET editor_id=:task_inactive WHERE editor_id=:user_id AND event_id=:event_id")){    
                  $update_tasks->bindParam(':task_inactive', $task_inactive);
                  $update_tasks->bindParam(':user_id', $user_id);
                  $update_tasks->bindParam(':event_id', $event_id);
                  $update_tasks->execute();
                  if($update_tasks){
                    $db -> commit (); 
                    $response["status"] = 200;
                    $response["message"] = "User entfernt und Tasks freigegeben.";
                    echo json_encode($response);
                  }else{
                    $db -> rollBack (); 
                    $response["status"] = 400;
                    $response["message"] = "Oops. Da ist ein Fehler aufgetreten!";
                    echo json_encode($response);
                  }
                }else{
                  $db -> rollBack (); 
                  $response["status"] = 400;
                  $response["message"] = "Oops. Da ist ein Fehler aufgetreten!";
                  echo json_encode($response);
                }  
                }else{
                  $db -> rollBack (); 
                  $response["status"] = 400;
                  $response["message"] = "Oops. Da ist ein Fehler aufgetreten!";
                  echo json_encode($response);
                }
}else{
      $response["status"] = 400;
      $response["message"] = "Oops. Da ist ein Fehler aufgetreten!";
      echo json_encode($response);
      }
      $db=null;
}else{
   $response["status"]=400;
   $response["message"]="Es wurden nicht alle Datensätze übertragen!";
   echo json_encode($response);
}
?>