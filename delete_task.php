<?php 
$response= array();

if(isset($_POST['task_id'])&&isset($_POST['admin_id'])){

$task_id = $_POST['task_id'];
$admin_id = $_POST['admin_id'];
$no_editor=-1;

require_once 'db_connect.php';

if($check=$db->prepare("SELECT editor_id FROM task WHERE task_id=:task_id")){
  $db->beginTransaction();
  $check->bindParam(':task_id', $task_id);
  $check->execute();  

  foreach ($check as $row) {
    $editor_id=$row["editor_id"];
  }
   
  if($editor_id==$admin_id||$editor_id==$no_editor){
    if($delete_task=$db->prepare("DELETE FROM task WHERE task_id=:task_id")){
    $delete_task->bindParam(':task_id', $task_id);
    $delete_task->execute();  

    if($delete_task){
    $db->commit();
    $response["status"]=200;
    $response["message"]="Aufgabe erfolgreich gelöscht.";
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
    $response["status"]=400;
    $response["message"]="Die Aufgabe wird bearbeitet. Sie können sie nicht löschen!";
    echo json_encode($response);
  }
}else{
      $db->rollBack();
      $response["status"]=400;
      $response["message"]="Oops. Versuchen Sie es später noch einmal.";
      echo json_encode($response);
    }
$db=null;
}else{
  $response["status"]=400;
    $response["message"]="Es wurden nicht alle Datensätze übertragen!";
    echo json_encode($response);
}
?>