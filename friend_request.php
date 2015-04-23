<?php 
$response= array();

if(isset($_POST['usera_id'])&&isset($_POST['userb_id'])){

    $usera_id = $_POST['usera_id'];
    $userb_id = $_POST['userb_id'];
    $status_friended=2;
    $status_open=0;
    $status_unfriended=1;

    require_once 'db_connect.php';

    $check=$db->prepare("SELECT * FROM friends WHERE ((user_a,user_b)= (:usera_id,:userb_id) OR (user_a,user_b)= (:userb_id,:usera_id)) AND status =:status");
    $updateStatus= $db->prepare("UPDATE friends SET status=:status WHERE ((user_a,user_b)= (:usera_id,:userb_id) OR (user_a,user_b)= (:userb_id,:usera_id))");

    if($check){
        //Check if already Friendrequest sended
        $db->beginTransaction();
        $check->bindParam(':usera_id', $usera_id);
        $check->bindParam(':userb_id', $userb_id);
        $check->bindParam(':status', $status_open);
        $check->execute();

        if(($check->rowCount())==0){
            //Check if already Friended
            $check->bindParam(':usera_id', $usera_id);
            $check->bindParam(':userb_id', $userb_id);
            $check->bindParam(':status', $status_friended);
            $check->execute();
            if(($check-> rowCount())==0){
                    if($insert =$db->prepare("INSERT INTO friends (user_a, user_b, status) VALUES (:usera_id,:userb_id,:status)")){
                //Send Friendrequest
                $insert->bindParam(':usera_id', $usera_id);
                $insert->bindParam(':userb_id', $userb_id);
                $insert->bindParam(':status', $status_open);
                $insert->execute();
                if($insert){
                    $db-> commit();
                    $response["status"] = 200;
                    $response["message"] = "Freundschaftsanfrage erfolgreich gesendet.";
                    echo json_encode($response);
                }else{
                    $db -> rollBack();
                    $response["status"] = 400;
                    $response["message"] = "Oops! Versuch es später noch einmal.";  
                    echo json_encode($response);
                }
            }else{
                $db -> rollBack();
                $response["status"] = 400;
                $response["message"] = "Oops! Versuch es später noch einmal.";  
                echo json_encode($response);
            }
            }else{
                $db -> rollBack();
                $response["status"] = 400;
                $response["message"] = "Ihr seid schon befreundet!";  
                echo json_encode($response);
            }
            }else{
                if($updateStatus){
                    $updateStatus->bindParam(':usera_id', $usera_id);
                    $updateStatus->bindParam(':userb_id', $userb_id);
                    $updateStatus->bindParam(':status', $status_friended);
                    $updateStatus->execute();
                    if($updateStatus){
                        $db-> commit();
                        $response["status"] = 200;
                        $response["message"] = "Ihr seid jetzt Freunde!";
                        echo json_encode($response);
                    }else{
                        $db -> rollBack();
                        $response["status"] = 400;
                        $response["message"] = "Oops! Versuch es später noch einmal.";  
                        echo json_encode($response);
                    }
                }else{
                    $db -> rollBack();
                    $response["status"] = 400;
                    $response["message"] = "Oops! Versuch es später noch einmal.";  
                    echo json_encode($response);
                }
            }
    }else{
        $response["status"] = 400;
        $response["message"] = "Oops! Versuch es später noch einmal.";  
        echo json_encode($response);
    }
    $db=null;
}else {
        $response["status"] = 400;
        $response["message"] = "Es wurden nicht alle Datensätze übertragen!";  
        echo json_encode($response);
    }
?>