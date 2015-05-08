<?php 
$response= array();

if(isset($_POST['admin_id'])){

    require_once 'db_connect.php';
    $db = new DB_CONNECT();

    $admin_id = $_POST['admin_id'];
    $status_open=0;

    $checkForNotification= mysql_query("SELECT * FROM friends WHERE user_b='$admin_id' AND status='$status_open'");
    
    if(mysql_num_rows($checkForNotification)>0){
        $response["status"] = 200;
        $response["message"] = "Freundschaftsanfrage erhalten!";
        echo json_encode($response);
}
}else {
        $response["status"] = 400;
        $response["message"] = "Oops! Versuch es später noch einmal";  
        echo json_encode($response);
    }
?>