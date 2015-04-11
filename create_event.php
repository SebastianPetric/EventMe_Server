<?php 

$response = array();

if (isset($_POST['name']) && isset($_POST['owner_id']) && isset($_POST['date'])&& isset($_POST['location'])) {

	$name = $_POST['name'];
    $owner_id = $_POST['owner_id'];
    $date = $_POST['date'];
    $location = $_POST['location'];
    
    require_once 'db_connect.php';

    $db = new DB_CONNECT();

    $result=mysql_query("INSERT INTO event (name,location, date, owner) VALUES('$name', '$location', '$date', '$owner_id')");

    if($result){
    	$response["status"]=200;
    	$response["message"]="Event erstellt";
        echo json_encode($response);
    }else{
    	$response["status"]=400;
    	$response["message"]="Event konnte nicht erstellt werden. Versuchen Sie es später noch einmal.";
        echo json_encode($response);
    }

}else{
	$response["status"]=400;
   	$response["message"]="Oops. Versuchen Sie es später noch einmal.";
    echo json_encode($response);
}
?>