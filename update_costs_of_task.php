<?php 

$response=array();

if(isset($_POST['task_id'])&&isset($_POST['editor_id'])&&isset($_POST['costs_of_task'])&&isset($_POST['type_of_update'])){

$task_id= $_POST['task_id'];
$editor_id=$_POST['editor_id'];
$costs_of_task=round(($_POST['costs_of_task']),2);
$type_of_update=$_POST['type_of_update'];
$status_inactive=-1;

require_once 'db_connect.php';

$check=$db->prepare("SELECT * FROM task WHERE task_id=:task_id AND editor_id=:status");
$update= $db->prepare("UPDATE task SET cost=:costs_of_task WHERE task_id=:task_id");

if($check){
			// Check if Task isn't Locked by Someone yet
			$db->beginTransaction();
        	$check->bindParam(':task_id', $task_id);
        	$check->bindParam(':status', $status_inactive);
        	$check->execute();
        	if(($check->rowCount())==0){
        		//Check if it is Locked by You
        		$check->bindParam(':task_id', $task_id);
        		$check->bindParam(':status', $editor_id);
        		$check->execute();

        		if(($check->rowCount())>0){
        			if($update){
        			if(intval($type_of_update)==0){
        				//Total Costs
        					$update->bindParam(':task_id', $task_id);
        					$update->bindParam(':costs_of_task', $costs_of_task);
        					$update->execute();

        					if($update){
								$db->commit();
        						$response["status"] = 200;
    							$response["message"] = "Kosten erfolgreich aktualisiert!";  
    							echo json_encode($response);
        					}else{
        						$db->rollBack();
        						$response["status"] = 400;
    							$response["message"] = "Oops! Versuch es später noch einmal!";  
    							echo json_encode($response);
        					}	
        			}else if(intval($type_of_update)==1){
        					//Add Costs
							$check->bindParam(':task_id', $task_id);
        					$check->bindParam(':status', $editor_id);
        					$check->execute();
        					foreach ($check as $rowCosts) {
        						$total= round(($rowCosts['cost']+$costs_of_task),2);
        					}
							$update->bindParam(':task_id', $task_id);
        					$update->bindParam(':costs_of_task', $total);
        					$update->execute();

        					if($update){
								$db->commit();
        						$response["status"] = 200;
    							$response["message"] = "Kosten erfolgreich aktualisiert!";  
    							echo json_encode($response);
        					}else{
        						$db->rollBack();
        						$response["status"] = 400;
    							$response["message"] = "Oops! Versuch es später noch einmal!";  
    							echo json_encode($response);
        					}	
        			}
        	}else{
        			$db->rollBack();
        			$response["status"] = 400;
    				$response["message"] = "Oops! Versuch es später noch einmal!";  
    				echo json_encode($response);
        			}
        		}else{
        			$db->rollBack();
        			$response["status"] = 400;
        			$response["message"] = "Jemand anderes bearbeitet diese Aufgabe bereits!";  
        			echo json_encode($response);
        		}
        	}else{
        		$db->rollBack();
        		$response["status"] = 400;
        		$response["message"] = "Diese Aufgabe muss erst zugeordnet werden!";  
        		echo json_encode($response);
        	}
}else{
	$response["status"] = 400;
    $response["message"] = "Oops! Versuch es später noch einmal!";  
    echo json_encode($response);
}
$db=null;
}else{
	$response["status"] = 400;
    $response["message"] = "Es wurden nicht alle Datensätze übertragen!";  
    echo json_encode($response);
}
?>