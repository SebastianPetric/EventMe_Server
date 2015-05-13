<?php 

$response = array();

if (isset($_POST['name']) && isset($_POST['owner_id']) && isset($_POST['date'])&& isset($_POST['location'])) {

    require_once 'db_connect.php';

	$name = $_POST['name'];
    $owner_id = $_POST['owner_id'];
    $date = $_POST['date'];
    $location = $_POST['location'];
        
    if($get_event_id = $db->prepare("SELECT event_id FROM event WHERE owner=:owner_id AND name=:name AND location=:location AND date=:date")){
                $db->beginTransaction();
                $get_event_id->bindParam(':name', $name);
                $get_event_id->bindParam(':owner_id', $owner_id);
                $get_event_id->bindParam(':date', $date);
                $get_event_id->bindParam(':location', $location);
                $get_event_id->execute();
                if(($get_event_id->rowCount())>0){ 
                    $db -> rollBack ();
                    $response["status"]=400;
                    $response["message"]="Das Event gibt es bereits";
                    echo json_encode($response);
                }else{
                    if($create_event = $db->prepare("INSERT INTO event (name,location, date, owner) VALUES(:name, :location, :date, :owner_id)")){
                         $create_event->bindParam(':name', $name);
                         $create_event->bindParam(':owner_id', $owner_id);
                         $create_event->bindParam(':date', $date);
                         $create_event->bindParam(':location', $location);
                         $create_event->execute();                       
                         if ($create_event) {
                                $get_event_id->bindParam(':name', $name);
                                $get_event_id->bindParam(':owner_id', $owner_id);
                                $get_event_id->bindParam(':date', $date);
                                $get_event_id->bindParam(':location', $location);
                                $get_event_id->execute();
                                foreach ($get_event_id as $row){
                                    $event_id= $row["event_id"];
                                } 
                                if($create_event = $db->prepare("INSERT INTO event_user (event_id,user_id) VALUES (:event_id,:owner_id)")){
                                    $create_event->bindParam(':event_id', $event_id);
                                    $create_event->bindParam(':owner_id', $owner_id);
                                    $create_event->execute();
                                    if($create_event){
                                        $db -> commit ();
                                        $response["status"] = 200;
                                        $response["message"] = "Event erstellt.";
                                        echo json_encode($response);
                                    }else{
                                        $db -> rollBack ();
                                        $response["status"] = 400;
                                        $response["message"] = "Event konnte nicht erstellt werden. Versuchen Sie es später noch einmal.";
                                        echo json_encode($response);
                                    }
                                }else{
                                    $db -> rollBack ();
                                    $response["status"]=400;
                                    $response["message"]="Oops. Versuchen Sie es später noch einmal.";
                                    echo json_encode($response);
                                }    
                        }else{
                            $db -> rollBack ();
                            $response["status"]=400;
                            $response["message"]="Oops. Versuchen Sie es später noch einmal.";
                            echo json_encode($response);
                        } 
                    }else{
                        $db -> rollBack ();
                        $response["status"]=400;
                        $response["message"]="Oops. Versuchen Sie es später noch einmal.";
                        echo json_encode($response);
                    }
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