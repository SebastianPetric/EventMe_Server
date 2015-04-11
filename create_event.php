<?php 

$response = array();

if (isset($_POST['name']) && isset($_POST['owner_id']) && isset($_POST['date'])&& isset($_POST['location'])) {

	$name = mysql_real_escape_string($_POST['name']);
    $owner_id = $_POST['owner_id'];
    $date = $_POST['date'];
    $location = mysql_real_escape_string($_POST['location']);
    
    require_once 'db_connect.php';

    $db = new DB_CONNECT();

    $checkDublicate=mysql_query("SELECT event_id FROM event WHERE owner='$owner_id' AND name='$name' AND location='$location' AND date='$date'");
    
    if(mysql_num_rows($checkDublicate)>0){
        
        $response["status"]=400;
        $response["message"]="Das Event gibt es bereits";
        echo json_encode($response);
    
    }else{

    $result=mysql_query("INSERT INTO event (name,location, date, owner) VALUES('$name', '$location', '$date', '$owner_id')");

    if($result){
        $getEventID=mysql_query("SELECT event_id FROM event WHERE owner='$owner_id' AND name='$name' AND location='$location' AND date='$date'");
        $event_id= mysql_fetch_array($getEventID)["event_id"];
        $updateEventUser=mysql_query("INSERT INTO event_user (event_id,user_id) VALUES ('$event_id','$owner_id')");
        
        $response["status"]=200;
        $response["message"]="Event erstellt";
        echo json_encode($response);
    }else{
        $response["status"]=400;
        $response["message"]="Event konnte nicht erstellt werden. Versuchen Sie es später noch einmal.";
        echo json_encode($response);
    }
    }
}else{
	$response["status"]=400;
   	$response["message"]="Oops. Versuchen Sie es später noch einmal.";
    echo json_encode($response);
}
?>