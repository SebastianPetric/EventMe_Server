<?php 
$response= array();

if(isset($_POST['task_id'])&&isset($_POST['admin_id'])&&isset($_POST['comment'])){

$task_id = $_POST['task_id'];
$admin_id = $_POST['admin_id'];
$comment = $_POST['comment'];

require_once 'db_connect.php';

if($insert_comment=$db->prepare("INSERT INTO task_history (task_id,user_id,comment) VALUES (:task_id,:user_id,:comment)")){
                $db->beginTransaction();
                $insert_comment->bindParam(':task_id', $task_id);
                $insert_comment->bindParam(':user_id', $admin_id);              
                $insert_comment->bindParam(':comment', $comment);
                $insert_comment->execute();
                
                if($insert_comment){
                  $db->commit();
                  $response["status"]=200;
                  $response["message"]="Erfolgreich kommentiert.";
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
$db=null;
}else{
  $response["status"]=400;
    $response["message"]="Es wurden nicht alle Datensätze übertragen!";
    echo json_encode($response);
}
?>