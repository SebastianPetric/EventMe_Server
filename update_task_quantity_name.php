<?php 

$response= array();

if(isset($_POST['task_id'])&&isset($_POST['admin_id'])&&isset($_POST['task'])&&isset($_POST['quantity'])){

$task_id = $_POST['task_id'];
$editor_id = $_POST['admin_id'];
$task = $_POST['task'];
$quantity = $_POST['quantity'];
$status_inactive = -1;
$task_name_temp;
$task_quantity_temp;

require_once 'db_connect.php';

if($check_editor=$db->prepare("SELECT * FROM task WHERE task_id=:task_id AND editor_id=:editor")){
        $db->beginTransaction();
        $check_editor->bindParam(':task_id', $task_id);
        $check_editor->bindParam(':editor', $editor_id);
        $check_editor->execute();

        if(($check_editor->rowCount())==0){
            $check_editor->bindParam(':task_id', $task_id);
            $check_editor->bindParam(':editor', $status_inactive);
            $check_editor->execute();

            if(($check_editor->rowCount())>0){
                $response["status"]=400;
                $response["message"]="Diese Aufgabe muss erst zugeordnet werden!";
                echo json_encode($response);
            }else{
                $response["status"]=400;
                $response["message"]="Die Aufgabe wird derzeit von jemanden bearbeitet.";
                echo json_encode($response);
            }
        }else{
          foreach ($check_editor as $row) {
            $task_name_temp=$row['task'];
            $task_quantity_temp=$row['quantity'];
          }

if($task!=""){
if($update_task=$db->prepare("UPDATE task SET task=:task_name WHERE task_id=:task_id")){
                $update_task->bindParam(':task_id', $task_id);
                $update_task->bindParam(':task_name', $task);
                $update_task->execute();
                if($update_task){
                  if($update_history=$db->prepare("INSERT INTO task_history (task_id,user_id,comment) VALUES (:task_id,:user_id,:comment)")){
                  $update_history->bindParam(':task_id', $task_id);
                  $update_history->bindParam(':user_id', $editor_id);
                  $comment="hat den Namen der Aufgabe von " ."\"". $task_name_temp."\"". " zu "."\"". $task."\"". " geändert.";
                  $update_history->bindParam(':comment', $comment);
                  $update_history->execute();
                    if($update_history){
                        $db->commit();
                        $response["status"]=200;
                        $response["message"]="Name der Aufgabe geändert.";
                        echo json_encode($response);
                    }else{
                      $db->rollBack();
                      $response["status"]=400;
                      $response["message"]="Oops. Versuchen Sie es später noch einmal.";
                      echo json_encode($response);
                    }
                  }else{
                    $db->rollBack();
                    $response["status"]=400;
                    $response["message"]="Oops. Versuchen Sie es später noch einmal.";
                    echo json_encode($response);
                  }
                }else{
                  $db->rollBack();
                  $response["status"]=400;
                  $response["message"]="Oops. Versuchen Sie es später noch einmal.";
                  echo json_encode($response);
                }
}else{
    $db->rollBack();
    $response["status"]=400;
    $response["message"]="Oops. Versuchen Sie es später noch einmal.";
    echo json_encode($response);
    }
}
if($quantity!=""){
       if($update_quantity=$db->prepare("UPDATE task SET quantity=:quantity WHERE task_id=:task_id")){
                $update_quantity->bindParam(':task_id', $task_id);
                $update_quantity->bindParam(':quantity', $quantity);
                $update_quantity->execute();
                if($update_quantity){
                     if($update_history=$db->prepare("INSERT INTO task_history (task_id,user_id,comment) VALUES (:task_id,:user_id,:comment)")){
                        $update_history->bindParam(':task_id', $task_id);
                        $update_history->bindParam(':user_id', $editor_id);
                        $comment="hat die Menge von " ."\"". $task_quantity_temp."\"". " auf "."\"". $quantity."\"". " geändert.";
                        $update_history->bindParam(':comment', $comment);
                        $update_history->execute();
                    if($update_history){
                       $db->commit();
                       $response["status"]=200;
                       $response["message"]="Menge erfolgreich geändert.";
                       echo json_encode($response);
                    }else{
                      $db->rollBack();
                      $response["status"]=400;
                      $response["message"]="Oops. Versuchen Sie es später noch einmal.";
                      echo json_encode($response);
                    }
                  }else{
                    $db->rollBack();
                    $response["status"]=400;
                    $response["message"]="Oops. Versuchen Sie es später noch einmal.";
                    echo json_encode($response);
                  }
                }else{
                  $db->rollBack();
                  $response["status"]=400;
                  $response["message"]="Oops. Versuchen Sie es später noch einmal.";
                  echo json_encode($response);
                }
}else{
    $db->rollBack();
    $response["status"]=400;
    $response["message"]="Oops. Versuchen Sie es später noch einmal.";
    echo json_encode($response);
    } 
}
}
}
$db = null;  
}else{
	$response["status"]=400;
	$response["message"]="Es wurden nicht alle Datensätze übertragen!";
	echo json_encode($response);
}
?>