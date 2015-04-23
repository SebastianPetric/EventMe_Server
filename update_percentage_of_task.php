<?php 

$response=array();

if(isset($_POST['task_id'])&&isset($_POST['editor_id'])&&isset($_POST['percentage_of_task'])){

$task_id= $_POST['task_id'];
$editor_id=$_POST['editor_id'];
$percentage=$_POST['percentage_of_task'];
$status_inactive=-1;

require_once 'db_connect.php';

$check= $db->prepare("SELECT * FROM task WHERE task_id=:task_id AND editor_id=:editor");

			if($check){
				//Check if Task has no editor
				$db->beginTransaction();
        		$check->bindParam(':task_id', $task_id);
        		$check->bindParam(':editor', $status_inactive);
        		$check->execute();

        		if(($check-> rowCount())>0){
        			$db->rollBack();
					$response["status"] = 400;
        			$response["message"] = "Du musst dich erst für diese Aufgabe locken!";  
        			echo json_encode($response);
        		}else{
        			//Check if you are Editor of Task
        			$check->bindParam(':task_id', $task_id);
        			$check->bindParam(':editor', $editor_id);
        			$check->execute();
        			if(($check-> rowCount())>0){
        				if($result= $db->prepare("UPDATE task SET percentage=:percentage WHERE task_id=:task_id")){
							//Update Percentage
							$result->bindParam(':task_id', $task_id);
        					$result->bindParam(':percentage', $percentage);
        					$result->execute();
        					if($result){
        						$db->commit();
								$response["status"] = 200;
        						$response["message"] = "Status erfolgreich aktualisiert!";  
        						echo json_encode($response);
        					}else{
        						$db->rollBack();
        						$response["status"] = 400;
        						$response["message"] = "Oops! Versuch es später noch einmal";  
        						echo json_encode($response);
        					}
        				}
        			}else{
        				$db->rollBack();
						$response["status"] = 400;
        				$response["message"] = "Jemand anderes bearbeitet diese Aufgabe bereits!";  
        				echo json_encode($response);
					}
        		}
			}else{
				$response["status"] = 400;
        		$response["message"] = "Oops! Versuch es später noch einmal";  
        		echo json_encode($response);
			}
			$db=null;
		}else{
			$response["status"] = 400;
        	$response["message"] = "Es wurden nicht alle Datensätze übertragen!";  
        	echo json_encode($response);
		}


?>