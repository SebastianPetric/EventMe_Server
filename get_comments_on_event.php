<?php 
$response= array();

if(isset($_POST['event_id'])){

$event_id = $_POST['event_id'];
$no_comments="";

require_once 'db_connect.php';

$get_editor_name=$db->prepare("SELECT name FROM user WHERE user_id=:user_id");

if($get_comments=$db->prepare("SELECT * FROM event_history WHERE event_id=:event_id ORDER BY created_at")){
	$db->beginTransaction();
    $get_comments->bindParam(':event_id', $event_id);
    $get_comments->execute();  
    if(($get_comments->rowCount())>0){
    	
    	foreach ($get_comments as $rowComment) {
    	$editor_id_temp= $rowComment['user_id'];
    	$comment_temp=$rowComment["comment"];
        $timestamp = $rowComment["created_at"];
        $date_timestamp = strtotime($timestamp);
        $actual_date=date('H:i, M d', $date_timestamp);
    	
    	$get_editor_name->bindParam(':user_id', $editor_id_temp);
   		$get_editor_name->execute();

   		foreach ($get_editor_name as $rowEditor) {
   			$editor_name= $rowEditor['name'];
   		}
   		$str.=nl2br($actual_date .' | '.$editor_name .': '. $comment_temp."\n".'----------------------'."\n");
        $response["history"]=str_replace(array('<br />'), ' ', $str);
    	}
    	$db->commit();
    	$response["status"]=200;
    	$response["message"]="Aktualisiert.";
    	echo json_encode($response);
    }else{
    	$response["history"]=$no_comments;
    	$response["status"]=200;
    	$response["message"]="Zur Zeit keine Kommentare vorhanden.";
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