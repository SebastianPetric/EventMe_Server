<?php 

$response=array();

if(isset($_POST['task_id'])&&isset($_POST['editor_id'])){

require_once 'db_connect.php';

$task_id= $_POST['task_id'];
$editor_id=$_POST['editor_id'];
$status_inactive=-1;
$name_inactive="offen";

$check_if = $db->prepare("SELECT * FROM task WHERE task_id=:task_id AND editor_id=:status");
$edit_editor = $db->prepare("UPDATE task SET editor_id=:editor_id WHERE task_id=:task_id");

                if($check_if){
                //Check if Task is inactive
                $db->beginTransaction();
                $check_if->bindParam(':task_id', $task_id);
                $check_if->bindParam(':status', $status_inactive);
                $check_if->execute();
                if(($check_if->rowCount())>0){ 
                        if($edit_editor){
                          $edit_editor->bindParam(':task_id', $task_id);
                          $edit_editor->bindParam(':editor_id', $editor_id);
                          $edit_editor->execute();
                          if($edit_editor){
                                $getEditor = $db->query("SELECT * FROM user WHERE user_id='$editor_id'");
                                if(($getEditor->rowCount())>0){
                                        foreach ($getEditor as $row) {
                                                $getEditorName=$row["name"];
                                        }
                                        $db -> commit (); 
                                        $response["status"] = 200;
                                        $response["editor_name"]= $getEditorName;  
                                        $response["message"] = "Sie sind der neue Bearbeiter der Aufgabe!";
                                        echo json_encode($response);
                                }else {
                                      $db -> rollBack (); 
                                      $response["status"] = 400;
                                      $response["message"] = "Oops! Versuch es später noch einmal";  
                                      echo json_encode($response);  
                                }
                        }else {
                                $db -> rollBack (); 
                                $response["status"] = 400;
                                $response["message"] = "Oops! Versuch es später noch einmal";  
                                echo json_encode($response);  
                                }
                }else {
                        $db -> rollBack (); 
                        $response["status"] = 400;
                        $response["message"] = "Oops! Versuch es später noch einmal";  
                        echo json_encode($response);  
                        }
                }else{
                //Check if You Are Editor of Task or not
                $check_if->bindParam(':task_id', $task_id);
                $check_if->bindParam(':status', $editor_id);
                $check_if->execute();
                if(($check_if->rowCount())>0){
                        //You set the Task inactive
                        $edit_editor->bindParam(':task_id', $task_id);
                        $edit_editor->bindParam(':editor_id', $status_inactive);
                        $edit_editor->execute();

                        if($edit_editor){
                        $db -> commit (); 
                        $response["status"] = 200;
                        $response["editor_name"]= $name_inactive;    
                        $response["message"] = "Du hast die Aufgabe freigegeben!";
                        echo json_encode($response);
                        }else{
                        $db -> rollBack (); 
                        $response["status"] = 400;
                        $response["message"] = "Oops! Versuch es später noch einmal";  
                        echo json_encode($response);
                        }               
                }else{
                        $db -> rollBack (); 
                        $response["status"] = 400;
                        $response["message"] = "Die Aufgabe wird schon bearbeitet!";  
                        echo json_encode($response);
                     }             
                }
}else{
        $response["status"] = 400;
        $response["message"] = "Oops! Versuch es später noch einmal";  
        echo json_encode($response);
        }    
        $db=null;       
}else{
        $response["status"]=400;
        $response["message"]="Es wurden nicht alle Datensätze übertragen!";
        echo json_encode($response);
        }
?>