<?php 
$response= array();

if(isset($_POST['user_id'])){

$user_id = $_POST['user_id'];
$status_open=1;

require_once 'db_connect.php';

$db = new DB_CONNECT();

$result= mysql_query("SELECT * FROM event WHERE owner='$user_id' AND status='$status_open'");

if(mysql_num_rows($result)>0){
	
	$response["events"] = array();
 	$event = array();
	
	while ($row = mysql_fetch_array($result)){
 	
 	$event_id=$row["event_id"];
	$numberOrganizersOfEvent=mysql_query("SELECT * FROM event_user WHERE event_id='$event_id'");
	$event["num_organizers_event"]=mysql_num_rows($numberOrganizersOfEvent);

	$costsOfEvent=mysql_query("SELECT * FROM task WHERE event_id='$event_id'");
	if(mysql_num_rows($costsOfEvent)>0){
		$event["costs_of_event"]=0;
		while ($rowCosts = mysql_fetch_array($costsOfEvent)) {
		$event["costs_of_event"]+=$rowCosts["cost"];
	}
	}
	else{
	$event["costs_of_event"]=0;
	}
	//$percentageOfEvent=mysql_query("SELECT * FROM task WHERE event_id='$event_id'");

 	$event["event_id"]=$event_id;
 	$event["name"]=$row["name"];
 	$event["location"]=$row["location"];
 	$event["date"]=$row["date"];
 	array_push($response["events"], $event);
 }
}
 $response["status"]=200;
 $response["message"]="Alle Events aktualisiert.";
 echo json_encode($response);
}else{
 $response["status"]=400;
 $response["message"]="Fehler. Versuchen Sie es später noch ein Mal!";
 echo json_encode($response);
}

?>