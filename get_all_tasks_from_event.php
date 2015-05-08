<?php 
$response=array();

if(isset($_POST['event_id'])){

$event_id = $_POST['event_id'];
$status_inactive="offen";

require_once 'db_connect.php';

if($get_task_data= $db->prepare("SELECT * FROM task WHERE event_id=:event_id ORDER BY percentage")){
	$db->beginTransaction();
    $get_task_data->bindParam(':event_id', $event_id);
    $get_task_data->execute();   

    if(($get_task_data->rowCount())>0){
    	$response["tasks"] = array();
    	$task=array();
		foreach ($get_task_data as $row) {
    		$task["task_id"]=$row["task_id"];
			$task["event_id"]=$row["event_id"];
			$task["task"]=$row["task"];
			$task["quantity"]=$row["quantity"];
			$task["description"]=$row["description"];
			$task["costs_of_task"]=$row["cost"];
			$task["percentage_of_task"]=$row["percentage"];
			$task["editor_id"]=$row["editor_id"];
			$editor_id=$row["editor_id"];

			if($get_editor= $db->prepare("SELECT name FROM user WHERE user_id=:editor_id")){
				$get_editor->bindParam(':editor_id', $editor_id);
    			$get_editor->execute(); 
    			if(($get_editor->rowCount())>0){
    				foreach ($get_editor as $row_name) {
    					$task["editor_name"]=$row_name["name"];
    				}
    			}else{
    				$task["editor_name"]=$status_inactive;
    			}
			}else{
				$db -> rollBack ();
				$response["status"]=400;
    			$response["message"]="Oops. Versuchen Sie es später noch einmal.";
    			echo json_encode($response);
			}
			array_push($response["tasks"], $task);
    	}
    	$db -> commit ();
    	$response["status"]=200;
 		$response["message"]="Aufgaben aktualisiert.";
 		echo json_encode($response);
    }else{
    	$db -> rollBack ();
 		$response["status"]=400;
 		$response["message"]="Keine Aufgaben für dieses Event vorhanden.";
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