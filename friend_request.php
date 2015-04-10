<?php 
$response= array();

if(isset($_POST['usera_id'])&&isset($_POST['userb_id'])){

    $usera_id = $_POST['usera_id'];
    $userb_id = $_POST['userb_id'];
    $status_friended=2;
    $status_open=0;
    $status_unfriended=1;

    require_once 'db_connect.php';

    $db = new DB_CONNECT();

    $checkIfAlreadyFriendedrequestSended= mysql_query("SELECT * FROM friends WHERE ((user_a,user_b)= ('$usera_id','$userb_id') OR (user_a,user_b)= ('$userb_id','$usera_id'))");
    $checkIfRequestIsOpen= mysql_query("SELECT * FROM friends WHERE ((user_a,user_b)= ('$usera_id','$userb_id') OR (user_a,user_b)= ('$userb_id','$usera_id')) AND status ='$status_open'");
    $checkIfFriendshipIsTopic= mysql_query("SELECT * FROM friends WHERE ((user_a,user_b)= ('$usera_id','$userb_id') OR (user_a,user_b)= ('$userb_id','$usera_id')) AND status ='$status_unfriended'");

    if(mysql_num_rows($checkIfAlreadyFriendedrequestSended)==0){

    $result =mysql_query("INSERT INTO friends (user_a, user_b, status) VALUES ('$usera_id','$userb_id',$status_open)");

    if ($result) {
        $response["status"] = 200;
        $response["message"] = "Freundschaftsanfrage erfolgreich gesendet";
        echo json_encode($response);
    } else {
        $response["status"] = 400;
        $response["message"] = "Oops! Versuch es später noch einmal";  
        echo json_encode($response);
    }
}else if(mysql_num_rows($checkIfRequestIsOpen)>0){
     $updateStatus= mysql_query("UPDATE friends SET status='$status_friended' WHERE ((user_a,user_b)= ('$usera_id','$userb_id') OR (user_a,user_b)= ('$userb_id','$usera_id'))");
     $response["status"] = 200;
     $response["message"] = "Ihr seid jetzt Freunde!";  
     echo json_encode($response);
}else if(mysql_num_rows($checkIfFriendshipIsTopic)>0){
        $updateStatus= mysql_query("UPDATE friends SET status='$status_open' WHERE ((user_a,user_b)= ('$usera_id','$userb_id') OR (user_a,user_b)= ('$userb_id','$usera_id'))");
        $response["status"] = 200;
        $response["message"] = "Freundschaftsanfrage erfolgreich gesendet";  
        echo json_encode($response);
}else{
        $response["status"] = 400;
        $response["message"] = "Oops! Versuch es später noch einmal";  
        echo json_encode($response);
}
}else {
        $response["status"] = 400;
        $response["message"] = "Oops! Versuch es später noch einmal";  
        echo json_encode($response);
    }
?>