<?php 
$response= array();

if(isset($_POST['id'])){

 $event_id = $_POST['id'];

 require_once 'db_connect.php';
    $get_editor_name=$db->prepare("SELECT name FROM user WHERE user_id=:editor_id");

    if($get_history=$db->prepare("SELECT * FROM event_history WHERE event_id=:event_id ORDER BY created_at DESC")){
                    $db->beginTransaction();
                    $get_history->bindParam(':event_id', $event_id);
                    $get_history->execute(); 

                    $response["history"] = array();
                    $history = array();  
                    
                    if(($get_history->rowCount())>0){
                        foreach ($get_history as $rowHistory) {
                            $history['id']=$rowHistory['event_history_id'];
                            $history['editor_id']=$rowHistory['user_id'];
                            $editor_temp=$rowHistory["user_id"];
                            $history['comment']=$rowHistory["comment"];
                            $timestamp = $rowHistory["created_at"];
                            $date_timestamp = strtotime($timestamp);
                            $actual_date=date('H:i, M d', $date_timestamp);
                            $history['date']= $actual_date;

                            $get_editor_name->bindParam(':editor_id', $editor_temp);
                            $get_editor_name->execute(); 

                            foreach ($get_editor_name as $rowEditorName) {
                                $history['name']= $rowEditorName["name"];
                            }
                            array_push($response["history"], $history);
                        }
                        $db->commit();
                        $response["status"]=200;
                        $response["message"]="Kommentare aktualisiert";
                        echo json_encode($response);
                    }else{
                        $db->rollBack();
                       $response["status"]=400;
                       $response["message"]="Keine Kommentare vorhanden.";
                       echo json_encode($response);
                    }
                }else{
                    $db->rollBack();
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