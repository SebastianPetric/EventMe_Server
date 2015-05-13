<?php 

$response= array();

if(isset($_POST['event_id'])&&isset($_POST['admin_id'])&&isset($_POST['name'])&&isset($_POST['location'])&&isset($_POST['date'])){

$event_id = $_POST['event_id'];
$admin_id = $_POST['admin_id'];
$name = $_POST['name'];
$location = $_POST['location'];
$date = $_POST['date'];
$no_date_change=-1;

$event_name_temp;
$event_date_temp;
$event_location_temp;

require_once 'db_connect.php';

if($check_owner=$db->prepare("SELECT * FROM event WHERE event_id=:event_id AND owner=:owner")){
        $db->beginTransaction();
        $check_owner->bindParam(':event_id', $event_id);
        $check_owner->bindParam(':owner', $admin_id);
        $check_owner->execute();

        if(($check_owner->rowCount())==0){
                $response["status"]=400;
                $response["message"]="Sie haben nicht die Berechtigung das Event zu bearbeiten!";
                echo json_encode($response);
        }else{
          foreach ($check_owner as $row) {
            $event_name_temp=$row['name'];
            $event_date_temp=$row['date'];
            $event_location_temp=$row['location'];
          }
if($location!=""){
if($update_location=$db->prepare("UPDATE event SET location=:location WHERE event_id=:event_id")){
                $update_location->bindParam(':location', $location);
                $update_location->bindParam(':event_id', $event_id);
                $update_location->execute();
                if($update_location){
                  if($update_history=$db->prepare("INSERT INTO event_history (event_id,user_id,comment) VALUES (:event_id,:user_id,:comment)")){
                  $update_history->bindParam(':event_id', $event_id);
                  $update_history->bindParam(':user_id', $admin_id);
                  $comment="hat die Location des Events von " ."\"". $event_location_temp."\"". " zu "."\"". $location."\"". " geändert.";
                  $update_history->bindParam(':comment', $comment);
                  $update_history->execute();
                    if($update_history){
                        $db->commit();
                        $response["status"]=200;
                        $response["message"]="Location des Events geändert.";
                        echo json_encode($response);
                    }else{
                      $db->rollBack();
                      $response["status"]=400;
                      $response["message"]="Oops. Versuchen Sie es später noch einmal.";
                      echo json_encode($response);
                    }
                  }else{
                    $db->rollBack();
                    $response["status"]=400;
                    $response["message"]="Oops. Versuchen Sie es später noch einmal.";
                    echo json_encode($response);
                  }
                }else{
                  $db->rollBack();
                  $response["status"]=400;
                  $response["message"]="Oops. Versuchen Sie es später noch einmal.";
                  echo json_encode($response);
                }
}else{
    $db->rollBack();
    $response["status"]=400;
    $response["message"]="Oops. Versuchen Sie es später noch einmal.";
    echo json_encode($response);
    }
}
if($name!=""){
if($update_name=$db->prepare("UPDATE event SET name=:name WHERE event_id=:event_id")){
                $update_name->bindParam(':name', $name);
                $update_name->bindParam(':event_id', $event_id);
                $update_name->execute();
                if($update_name){
                  if($update_history=$db->prepare("INSERT INTO event_history (event_id,user_id,comment) VALUES (:event_id,:user_id,:comment)")){
                  $update_history->bindParam(':event_id', $event_id);
                  $update_history->bindParam(':user_id', $admin_id);
                  $comment="hat den Namen des Events von " ."\"". $event_name_temp."\"". " zu "."\"". $name."\"". " geändert.";
                  $update_history->bindParam(':comment', $comment);
                  $update_history->execute();
                    if($update_history){
                        $db->commit();
                        $response["status"]=200;
                        $response["message"]="Name des Events geändert.";
                        echo json_encode($response);
                    }else{
                      $db->rollBack();
                      $response["status"]=400;
                      $response["message"]="Oops. Versuchen Sie es später noch einmal.";
                      echo json_encode($response);
                    }
                  }else{
                    $db->rollBack();
                    $response["status"]=400;
                    $response["message"]="Oops. Versuchen Sie es später noch einmal.";
                    echo json_encode($response);
                  }
                }else{
                  $db->rollBack();
                  $response["status"]=400;
                  $response["message"]="Oops. Versuchen Sie es später noch einmal.";
                  echo json_encode($response);
                }
}else{
    $db->rollBack();
    $response["status"]=400;
    $response["message"]="Oops. Versuchen Sie es später noch einmal.";
    echo json_encode($response);
    }
}
if($date!=-1){
if($update_date=$db->prepare("UPDATE event SET date=:date WHERE event_id=:event_id")){
                $update_date->bindParam(':date', $date);
                $update_date->bindParam(':event_id', $event_id);
                $update_date->execute();
                if($update_date){
                  if($update_history=$db->prepare("INSERT INTO event_history (event_id,user_id,comment) VALUES (:event_id,:user_id,:comment)")){
                  $update_history->bindParam(':event_id', $event_id);
                  $update_history->bindParam(':user_id', $admin_id);
                 
                  $oldDate = strtotime($event_date_temp);
                  $oldDateDayMonth=date('d.m', $event_date_temp);
                  $oldDateYear=date('Y', $event_date_temp)-1900;
                  $oldDate= $oldDateDayMonth.'.'.$oldDateYear;

                  $newdate = strtotime($date);
                  $dateDayMonth=date('d.m', $newdate);
                  $dateYear=date('Y', $newdate)-1900;
                  $newDate= $dateDayMonth.'.'.$dateYear;

                  $comment="hat das Datum des Events von " ."\"". $oldDate."\"". " auf "."\"". $newDate."\"". " geändert.";
                  $update_history->bindParam(':comment', $comment);
                  $update_history->execute();
                    if($update_history){
                        $db->commit();
                        $response["status"]=200;
                        $response["message"]="Datum des Events geändert.";
                        echo json_encode($response);
                    }else{
                      $db->rollBack();
                      $response["status"]=400;
                      $response["message"]="Oops. Versuchen Sie es später noch einmal.";
                      echo json_encode($response);
                    }
                  }else{
                    $db->rollBack();
                    $response["status"]=400;
                    $response["message"]="Oops. Versuchen Sie es später noch einmal.";
                    echo json_encode($response);
                  }
                }else{
                  $db->rollBack();
                  $response["status"]=400;
                  $response["message"]="Oops. Versuchen Sie es später noch einmal.";
                  echo json_encode($response);
                }
}else{
    $db->rollBack();
    $response["status"]=400;
    $response["message"]="Oops. Versuchen Sie es später noch einmal.";
    echo json_encode($response);
    }
}
}
}else{
  $response["status"]=400;
  $response["message"]="Oops. Versuchen Sie es später noch einmal.";
  echo json_encode($response);
}
$db = null;  
}else{
  $response["status"]=400;
  $response["message"]="Es wurden nicht alle Datensätze übertragen!";
  echo json_encode($response);
}
?>