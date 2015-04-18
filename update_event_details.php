<?php 
$response= array();

if(isset($_POST['admin_id'])&& isset($_POST['event_id'])){

$admin_id = $_POST['admin_id'];
$event_id = $_POST['event_id'];

require_once 'db_connect.php';

$db = new DB_CONNECT();

$result= mysql_query("SELECT * FROM event INNER JOIN event_user WHERE event.event_id='$event_id' AND event_user.user_id='$admin_id' AND event_user.event_id='$event_id'");

if(mysql_num_rows($result)>0){
	
	$response["events"] = array();
 	$event = array();
	
	while ($row = mysql_fetch_array($result)){
 	
	$numberOrganizersOfEvent=mysql_query("SELECT * FROM event_user WHERE event_id='$event_id'");
	$event["num_organizers_event"]=mysql_num_rows($numberOrganizersOfEvent);

	$costsOfEvent=mysql_query("SELECT * FROM task WHERE event_id='$event_id'");
	if(mysql_num_rows($costsOfEvent)>0){
		$event["costs_of_event"]=0;
		$event["percentage_of_event"]=0;
		$total=0;
		$maximum=((mysql_num_rows($costsOfEvent)*100));

		while ($rowCosts = mysql_fetch_array($costsOfEvent)) {
		$event["costs_of_event"]+=round(($rowCosts["cost"]),2);
		$total+=$rowCosts["percentage"];
		}
		$event["percentage_of_event"]= round(((100/$maximum)*$total),2);
	}
	else{
	$event["costs_of_event"]=0;
	$event["percentage_of_event"]=0;
	}
 	$event["event_id"]=$event_id;
 	$event["location"]=$row["location"];
 	$event["name"]=$row["name"];
 	$event["date"]=$row["date"];
 	array_push($response["events"], $event);
 }
 $response["status"]=200;
 //$response["message"]="Alle Events aktualisiert.";
 echo json_encode($response);
}else{
	$response["status"]=400;
 	//$response["message"]="Zur Zeit keine Events!";
 	echo json_encode($response);
}
 
}else{
 $response["status"]=400;
 //$response["message"]="Fehler. Versuchen Sie es später noch ein Mal!";
 echo json_encode($response);
}

?>