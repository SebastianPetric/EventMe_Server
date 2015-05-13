<?php 
$response= array();

if(isset($_POST['admin_id'])&& isset($_POST['event_id'])){

$admin_id = $_POST['admin_id'];
$event_id = $_POST['event_id'];
$event_user="event_user";
$task="task";

require_once 'db_connect.php';

if($get_all_events= $db->prepare("SELECT * FROM event INNER JOIN event_user WHERE event.event_id=:event_id AND event_user.user_id=:admin_id AND event_user.event_id=:event1_id")){
			$db->beginTransaction();
        	$get_all_events->bindParam(':event_id', $event_id);
        	$get_all_events->bindParam(':admin_id', $admin_id);
            $get_all_events->bindParam(':event1_id', $event_id);
        	$get_all_events->execute();

        	if(($get_all_events->rowCount())>0){
        		$response["events"] = array();
 				$event = array();
				foreach ($get_all_events as $row) {
					$event["event_id"]=$event_id;
 					$event["location"]=$row["location"];
 					$event["name"]=$row["name"];
 					$event["date"]=$row["date"];
				}
 				if($get_data_organizers=$db->prepare("SELECT * FROM event_user WHERE event_id=:event_id")){
 						//Get Data of all Event Users
        				$get_data_organizers->bindParam(':event_id', $event_id);
        				$get_data_organizers->execute();
        				$event["num_organizers_event"]=$get_data_organizers->rowCount();
        			}else{
        				$db->rollBack();
        				$response["status"] = 400;
    					$response["message"] = "Oops! Versuch es später noch einmal";  
    					echo json_encode($response);
        			}
        		if($get_data=$db->prepare("SELECT * FROM task WHERE event_id=:event_id")){
        			//Get Data of all Event Tasks
        			$get_data->bindParam(':event_id', $event_id);
        			$get_data->execute();

        			if(($get_data->rowCount())>0){
							$event["costs_of_event"]=0;
							$event["percentage_of_event"]=0;
							$total=0;
							$maximum=(($get_data->rowCount())*100);
        				foreach ($get_data as $row_data) {
							$event["costs_of_event"]+=round(($row_data["cost"]),2);
							$total+=$row_data["percentage"];
        				}
        				$event["percentage_of_event"]= round(((100/$maximum)*$total),2);
        			}else{
						$event["costs_of_event"]=0;
						$event["percentage_of_event"]=0;
					}
 					array_push($response["events"], $event);	
        		}
        		$db->commit();
        		$response["status"]=200;
 				$response["message"]="Event aktualisiert.";
 				echo json_encode($response);
        	}else{
        		$db->rollBack();
        		$response["status"]=400;
 				$response["message"]="Zur Zeit keine Events!";
 				echo json_encode($response);
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