<?php 

$response= array();

if(isset($_POST['event_id'])&&isset($_POST['editor_id'])&&isset($_POST['task'])&&isset($_POST['description'])&&isset($_POST['quantity'])&&isset($_POST['admin_id'])){

$event_id = $_POST['event_id'];
$editor_id = $_POST['editor_id'];
$task = $_POST['task'];
$description = $_POST['description'];
$quantity = $_POST['quantity'];
$admin_id=$_POST['admin_id'];
$empty_string="";
$inactive_editor=-1;

require_once 'db_connect.php';

if($create_task = $db->prepare("INSERT INTO task (event_id,task,editor_id,quantity) VALUES (:event_id,:task,:editor_id,:quantity)")){
                $db->beginTransaction();
                $create_task->bindParam(':event_id', $event_id);
                $create_task->bindParam(':task', $task);
                $create_task->bindParam(':editor_id', $editor_id);
                $create_task->bindParam(':quantity', $quantity);
                $create_task->execute();
                if ($create_task) {
                    if($description!=$empty_string){
                    if($get_task_id=$db->prepare("SELECT task_id FROM task WHERE event_id=:event_id AND task=:task AND editor_id=:editor_id AND quantity=:quantity")){
                        $get_task_id->bindParam(':event_id', $event_id);
                        $get_task_id->bindParam(':task', $task);
                        $get_task_id->bindParam(':editor_id', $editor_id);
                        $get_task_id->bindParam(':quantity', $quantity);
                        $get_task_id->execute();
                        foreach ($get_task_id as $row) {
                            $task_id= $row["task_id"];
                        }
                         if($insert_comment = $db->prepare("INSERT INTO task_history (task_id,user_id,comment) VALUES (:task_id,:user_id,:comment)")){
                            $insert_comment->bindParam(':task_id', $task_id);
                            $insert_comment->bindParam(':user_id', $admin_id);
                            $insert_comment->bindParam(':comment', $description);
                            $insert_comment->execute();
                             if($insert_comment){
                                //Add User as an organizer, if he isn't already in it
                                if($editor_id!=$inactive_editor){
                                  $check_if_user_is_in_event=$db->prepare("SELECT * FROM event_user WHERE event_id=:event_id AND user_id=:editor_id");
                                  $check_if_user_is_in_event->bindParam(':event_id', $event_id);
                                  $check_if_user_is_in_event->bindParam(':editor_id', $editor_id);
                                  $check_if_user_is_in_event->execute();
                                  if(($check_if_user_is_in_event->rowCount())==0){
                                    $add_friend_to_event=$db->prepare("INSERT INTO event_user (user_id,event_id) VALUES (:user_id,:event_id)");
                                    $add_friend_to_event->bindParam(':user_id', $editor_id);
                                    $add_friend_to_event->bindParam(':event_id', $event_id);
                                    $add_friend_to_event->execute();
                                    if($add_friend_to_event){
                                        $db -> commit ();
                                        $response["status"] = 200;
                                        $response["message"] = "Aufgabe erstellt.";
                                        echo json_encode($response);
                                    }else{
                                          $db -> rollBack ();
                                          $response["status"] = 400;
                                          $response["message"] = "Fehler beim erstellen der Aufgabe. Versuchen Sie es später noch ein Mal!";
                                          echo json_encode($response);
                                    }
                                  }
                                }
                                 $db -> commit ();
                                 $response["status"] = 200;
                                 $response["message"] = "Aufgabe erstellt.";
                                 echo json_encode($response);
                            }else {
                                $db -> rollBack ();
                                $response["status"] = 400;
                                $response["message"] = "Fehler beim erstellen der Aufgabe. Versuchen Sie es später noch ein Mal!";
                                echo json_encode($response);
                                }
                         }else{
                            $db -> rollBack ();
                            $response["status"] = 400;
                            $response["message"] = "Fehler beim erstellen der Aufgabe. Versuchen Sie es später noch ein Mal!";
                            echo json_encode($response);
                         }
                    }else{
                     $db -> rollBack ();
                     $response["status"] = 400;
                     $response["message"] = "Fehler beim erstellen der Aufgabe. Versuchen Sie es später noch ein Mal!";
                     echo json_encode($response);
                    }
                }else{
                    $db -> commit ();
                    $response["status"] = 200;
                    $response["message"] = "Aufgabe erstellt.";
                    echo json_encode($response);
                }
                }else{
                     $db -> rollBack ();
                     $response["status"] = 400;
                     $response["message"] = "Fehler beim erstellen der Aufgabe. Versuchen Sie es später noch ein Mal!";
                     echo json_encode($response);
                }
}else{
      $db -> rollBack ();
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