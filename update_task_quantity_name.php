<?php 

$response= array();

if(isset($_POST['task_id'])&&isset($_POST['editor_id'])&&isset($_POST['task'])&&isset($_POST['quantity'])){

$task_id = $_POST['task_id'];
$editor_id = $_POST['editor_id'];
$task = $_POST['task'];
$quantity = $_POST['quantity'];
$status_inactive = -1;

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
                $response["message"]="Du musst dich erst für die Aufgabe locken.";
                echo json_encode($response);
            }else{
                $response["status"]=400;
                $response["message"]="Die Aufgabe wird derzeit von jemanden bearbeitet.";
                echo json_encode($response);
            }
        }else{

if($task!=""){
if($update_task=$db->prepare("UPDATE task SET task=:task_name WHERE task_id=:task_id")){
                $update_task->bindParam(':task_id', $task_id);
                $update_task->bindParam(':task_name', $task);
                $update_task->execute();
                if($update_task){
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
}
if($quantity!=""){
       if($update_quantity=$db->prepare("UPDATE task SET quantity=:quantity WHERE task_id=:task_id")){
                $update_quantity->bindParam(':task_id', $task_id);
                $update_quantity->bindParam(':quantity', $quantity);
                $update_quantity->execute();
                if($update_quantity){
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