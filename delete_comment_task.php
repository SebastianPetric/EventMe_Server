<?php 

$response= array();

if(isset($_POST['id'])){

$history_id=$_POST['id'];

require_once 'db_connect.php';

if($delete_comment_task = $db->prepare("DELETE FROM task_history WHERE task_history_id=:history_id")){
				$db->beginTransaction();
        		$delete_comment_task->bindParam(':history_id', $history_id);
                $delete_comment_task->execute();
                if($delete_comment_task){
					$db -> commit (); 
                    $response["status"]=200;
                    $response["message"]="Kommentar erfolgeich gelöscht.";
                    echo json_encode($response);
                }else{
 					$db -> rollBack (); 
                    $response["status"]=400;
                    $response["message"]="Oops! Versuch es später noch einmal";
                    echo json_encode($response);
                }
}
$db=null;
}else{
  $response["status"]=400;
  $response["message"]="Es wurden nicht alle Datensätze übertragen!";
  echo json_encode($response);
  }
?>