<?php 
$response= array();

if(isset($_GET['admin_id'])){

$admin_id = $_GET['admin_id'];
$status_open=1;

require_once 'db_connect.php';

if($result= $db->prepare("SELECT * FROM event INNER JOIN event_user ON event.event_id= event_user.event_id AND event_user.user_id=:admin_id ORDER BY event.date")){
    $db->beginTransaction();
    $result->bindParam(':admin_id', $admin_id);
    $result->execute();           
    
    if(($result->rowCount())>0){
     $response["events"] = array();
     $event = array(); 
     foreach ($result as $row) {
        $event_id=$row["event_id"];
        if($get_organizers=$db->prepare("SELECT * FROM event_user WHERE event_id=:event_id")){
            //Get all Users who are in this event
            $get_organizers->bindParam(':event_id', $event_id);
            $get_organizers->execute();
            $event["num_organizers_event"]=$get_organizers->rowCount();

            if($get_tasks=$db->prepare("SELECT * FROM task WHERE event_id=:event_id")){
            //Get all Tasks in that event
            $get_tasks->bindParam(':event_id', $event_id);
            $get_tasks->execute();

                 if($get_tasks->rowCount()>0){
                    $event["costs_of_event"]=0;
                    $event["percentage_of_event"]=0;
                    $total=0;
                    $maximum=(($get_tasks->rowCount())*100);
                    foreach ($get_tasks as $rowCosts) {
                    $event["costs_of_event"]+=round(($rowCosts["cost"]),2);
                    $total+=$rowCosts["percentage"];
                    }
                    $event["percentage_of_event"]= round(((100/$maximum)*$total),2);
                 }else{
                    $event["costs_of_event"]=0;
                    $event["percentage_of_event"]=0;
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
        $event["event_id"]=$event_id;
        $event["name"]=$row["name"];
        $event["location"]=$row["location"];
        $event["date"]=$row["date"];
        array_push($response["events"], $event);
     }
     $db -> commit ();
     $response["status"]=200;
     $response["message"]="Alle Events aktualisiert.";
     echo json_encode($response);  
    }else{
        $db -> rollBack ();
        $response["status"]=400;
        $response["message"]="Bisher keine Events.";
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