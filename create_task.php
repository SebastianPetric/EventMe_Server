<?php 

$response= array();

if(isset($_POST['event_id'])&&isset($_POST['editor_id'])&&isset($_POST['task'])&&isset($_POST['description'])&&isset($_POST['quantity'])){

$event_id = $_POST['event_id'];
$editor_id = $_POST['editor_id'];
$task = $_POST['task'];
$description = $_POST['description'];
$quantity = $_POST['quantity'];
$empty_string="";

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
                            $insert_comment->bindParam(':user_id', $editor_id);
                            $insert_comment->bindParam(':comment', $description);
                            $insert_comment->execute();

                             if($insert_comment){
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