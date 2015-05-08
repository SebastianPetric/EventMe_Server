<?php 

$response= array();

if(isset($_POST['usera_id'])&&isset($_POST['userb_id'])){

$usera_id=$_POST['usera_id'];
$userb_id=$_POST['userb_id'];
$status_open=0;
$status_friended=2;

require_once 'db_connect.php';

$delete_friend= $db->prepare("DELETE FROM friends WHERE ((user_a,user_b)= (:usera_id,:userb_id) OR (user_a,user_b)= (:userb1_id,:usera1_id))");

if($check_if_delete_friend = $db->prepare("SELECT * FROM friends WHERE ((user_a,user_b)= (:usera_id,:userb_id) OR (user_a,user_b)= (:userb1_id,:usera1_id)) AND status=:status_friended")){ 
				$db->beginTransaction();
				$check_if_delete_friend->bindParam(':usera_id', $usera_id);
                $check_if_delete_friend->bindParam(':userb_id', $userb_id);
                $check_if_delete_friend->bindParam(':usera1_id', $usera_id);
                $check_if_delete_friend->bindParam(':userb1_id', $userb_id);
                $check_if_delete_friend->bindParam(':status_friended', $status_friended);
                $check_if_delete_friend->execute();
                if(($check_if_delete_friend->rowCount())>0){
                	if($delete_friend){
                		$delete_friend->bindParam(':usera_id', $usera_id);
                		$delete_friend->bindParam(':userb_id', $userb_id);
                		$delete_friend->bindParam(':usera1_id', $usera_id);
                		$delete_friend->bindParam(':userb1_id', $userb_id);
                		$delete_friend->execute();
                		if($delete_friend){
							$db -> commit();
							$response["status"]=200;
							$response["message"]="Freund gelöscht.";
							echo json_encode($response);
                		}else{
                			$db -> rollBack (); 
                			$response["status"]=400;
							$response["message"]="Oops! Versuch es später noch einmal";
							echo json_encode($response);
                		}
                	}else{
                		$db -> rollBack (); 
						$response["status"]=400;
						$response["message"]="Oops! Versuch es später noch einmal";
						echo json_encode($response);
					}
                }else{
                	if($check_if_deny_request = $db->prepare("SELECT * FROM friends WHERE (user_b,user_a)= (:usera_id,:userb_id) AND status=:status_open")){ 
						$check_if_deny_request->bindParam(':usera_id', $usera_id);
                		$check_if_deny_request->bindParam(':userb_id', $userb_id);
                		$check_if_deny_request->bindParam(':status_open', $status_open);
                		$check_if_deny_request->execute();
                		if(($check_if_deny_request->rowCount())>0){
                			$delete_friend->bindParam(':usera_id', $usera_id);
                			$delete_friend->bindParam(':userb_id', $userb_id);
                			$delete_friend->execute();
                		if($delete_friend){
							$db -> commit();
							$response["status"]=200;
							$response["message"]="Freundschaftsanfrage abgelehnt.";
							echo json_encode($response);
                		}else{
                			$db -> rollBack (); 
                			$response["status"]=400;
							$response["message"]="Oops! Versuch es später noch einmal";
							echo json_encode($response);
                		}
                		}else{
                			$db -> rollBack (); 
							$response["status"]=400;
							$response["message"]="Oops! Versuch es später noch einmal";
							echo json_encode($response);
						}
					}else{
						$db -> rollBack (); 
						$response["status"]=400;
						$response["message"]="Oops! Versuch es später noch einmal";
						echo json_encode($response);
					}	
                }
}else{
	$response["status"]=400;
	$response["message"]="Oops! Versuch es später noch einmal";
	echo json_encode($response);
}
$db=null;
}else{
	$response["status"]=400;
	$response["message"]="Es wurden nicht alle Datensätze übertragen!";
	echo json_encode($response);
	}
?>