<?php 

$response= array();

if(isset($_POST['admin_id'])&&isset($_POST['event_id'])){

$admin_id=$_POST['admin_id'];
$event_id=$_POST['event_id'];

require_once 'db_connect.php';

if($check_if_you_are_admin_of_event = $db->prepare("SELECT owner FROM event WHERE event_id=:event_id AND owner=:admin_id")){ 
        $db->beginTransaction();
        $check_if_you_are_admin_of_event->bindParam(':event_id', $event_id);
                $check_if_you_are_admin_of_event->bindParam(':admin_id', $admin_id);
                $check_if_you_are_admin_of_event->execute();
                if(($check_if_you_are_admin_of_event->rowCount())>0){
                  if($delete_event=$db->prepare("DELETE FROM event WHERE event_id=:event_id")){
                    $delete_event->bindParam(':event_id', $event_id);
                    $delete_event->execute();
                    if($delete_event){
                       if($delete_event_history=$db->prepare("DELETE FROM event_history WHERE event_id=:event_id")){
                          $delete_event_history->bindParam(':event_id', $event_id);
                          $delete_event_history->execute();
                          if($delete_event_history){
                      if($delete_user_event=$db->prepare("DELETE FROM event_user WHERE event_id=:event_id")){
                      $delete_user_event->bindParam(':event_id', $event_id);
                      $delete_user_event->execute();
                      if($delete_user_event){
                        if($select_tasks_from_event=$db->prepare("SELECT task_id FROM task WHERE event_id=:event_id")){
                          $select_tasks_from_event->bindParam(':event_id', $event_id);
                          $select_tasks_from_event->execute();
                          if(($select_tasks_from_event->rowCount())>0){
                            $delete_task_history=$db->prepare("DELETE FROM task_history WHERE task_id=:task_id");
                            foreach ($select_tasks_from_event as $row) {
                              $task_id_temp=$row['task_id'];
                              $delete_task_history->bindParam(':task_id', $task_id_temp);
                              $delete_task_history->execute();
                                if(!$delete_task_history){
                                  $db -> rollBack (); 
                                  $response["status"]=400;
                                  $response["message"]="Oops! Versuch es später noch einmal";
                                  echo json_encode($response);
                                }
                            }
                            if($delete_tasks_of_event=$db->prepare("DELETE FROM task WHERE event_id=:event_id")){
                                $delete_tasks_of_event->bindParam(':event_id', $event_id);
                                $delete_tasks_of_event->execute();
                                if($delete_tasks_of_event){
                                        $db -> commit (); 
                                        $response["status"]=200;
                                        $response["message"]="Event erfolgeich gelöscht.";
                                        echo json_encode($response);
                                }else{
                                  $db -> rollBack (); 
                                  $response["status"]=400;
                                  $response["message"]="Oops! Versuch es später noch einmal";
                                  echo json_encode($response);
                                }
                            }else{
                              $db -> rollBack (); 
                              $response["status"]=400;
                              $response["message"]="Oops! Versuch es später noch einmal";
                              echo json_encode($response);
                            }
                          }else{
                            $db -> commit (); 
                            $response["status"]=200;
                            $response["message"]="Event erfolgeich gelöscht.";
                            echo json_encode($response);
                          }
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
                  $db->rollBack();
                  $response["status"]=400;
                  $response["message"]="Oops! Versuch es später noch einmal";
                  echo json_encode($response);
                }
               }else{
                  $response["status"]=400;
                  $response["message"]="Sie haben nicht die Berechtigung dieses Event zu löschen!";
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
