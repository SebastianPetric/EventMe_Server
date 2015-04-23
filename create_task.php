<?php 

$response= array();

if(isset($_POST['event_id'])&&isset($_POST['editor_id'])&&isset($_POST['task'])&&isset($_POST['description'])&&isset($_POST['quantity'])){

$event_id = $_POST['event_id'];
$editor_id = $_POST['editor_id'];
$task = $_POST['task'];
$description = $_POST['description'];
$quantity = $_POST['quantity'];

require_once 'db_connect.php';

if($create_task = $db->prepare("INSERT INTO task (event_id,task,editor_id,description,quantity) VALUES (:event_id,:task,:editor_id,:description,:quantity)")){
                $db->beginTransaction();
				$create_task->bindParam(':event_id', $event_id);
                $create_task->bindParam(':task', $task);
                $create_task->bindParam(':editor_id', $editor_id);
                $create_task->bindParam(':description', $description);
                $create_task->bindParam(':quantity', $quantity);
                $create_task->execute();
                if ($create_task) {
                    $db -> commit ();
            		$response["status"] = 200;
            		$response["message"] = "Aufgabe erstellt.";
            		echo json_encode($response);
        		} else {
                    $db -> rollBack ();
            		$response["status"] = 400;
            		$response["message"] = "Fehler beim erstellen der Aufgabe. Versuchen Sie es später noch ein Mal!";
            		echo json_encode($response);
        		}
}else{
        $db -> rollBack ();
		$response["status"] = 400;
        $response["message"] = "Oops. Es ist ein Fehler aufgetreten!";
        echo json_encode($response);
}
$db = null;  
}else{
	$response["status"]=400;
	$response["message"]="Es wurden nicht alle Datensätze übertragen!";
	echo json_encode($response);
}
?>