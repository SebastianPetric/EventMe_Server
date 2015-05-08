<?php 
$response= array();

if(isset($_POST['event_id'])&&isset($_POST['admin_id'])&&isset($_POST['comment'])){

$event_id = $_POST['event_id'];
$admin_id = $_POST['admin_id'];
$comment = $_POST['comment'];

require_once 'db_connect.php';

if($insert_comment=$db->prepare("INSERT INTO event_history (event_id,user_id,comment) VALUES (:event_id,:user_id,:comment)")){
                $db->beginTransaction();
                $insert_comment->bindParam(':event_id', $event_id);
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