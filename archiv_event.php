<?php 

$response= array();

if(isset($_POST['admin_id'])&&isset($_POST['event_id'])){

$admin_id=$_POST['admin_id'];
$event_id=$_POST['event_id'];
$status_inactive=0;

require_once 'db_connect.php';

if($check_if_you_are_admin_of_event = $db->prepare("SELECT owner FROM event WHERE event_id=:event_id AND owner=:admin_id")){ 
        $db->beginTransaction();
        $check_if_you_are_admin_of_event->bindParam(':event_id', $event_id);
                $check_if_you_are_admin_of_event->bindParam(':admin_id', $admin_id);
                $check_if_you_are_admin_of_event->execute();
                if(($check_if_you_are_admin_of_event->rowCount())>0){
                    if($set_status_inactive=$db->prepare("UPDATE event SET status=$status_inactive WHERE event_id=:event_id")){
                      $set_status_inactive->bindParam(':event_id', $event_id);
                      $set_status_inactive->execute();
                      if($set_status_inactive){
                        $db->rollBack();
                        $response["status"]=200;
                        $response["message"]="Event erfolgreich archiviert";
                        echo json_encode($response);
                      }else{
                        $db->rollBack();
                        $response["status"]=400;
                        $response["message"]="Oops! Versuch es später noch einmal";
                        echo json_encode($response);
                      }
                    }else{
                       $db->rollBack();
                       $response["status"]=400;
                       $response["message"]="Oops! Versuch es später noch einmal";
                       echo json_encode($response);
                    }
                }else{
                  $response["status"]=400;
                  $response["message"]="Sie haben nicht die Berechtigung dieses Event zu archivieren!";
                  echo json_encode($response);
                }
}else{
  $response["status"]=400;
  $response["message"]="Oops! Versuch es später noch einmal";
  echo json_encode($response);
}
$db=null;
}else{
  $response["status"]=400;
  $response["message"]="Es wurden nicht alle Datensätze übertragen!";
  echo json_encode($response);
  }
?>
