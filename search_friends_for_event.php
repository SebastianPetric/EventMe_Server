<?php 

$response= array();

if(isset($_POST['search'])&&isset($_POST['admin_id'])&&isset($_POST['event_id'])){

$admin_id=$_POST['admin_id'];
$event_id=$_POST['event_id'];
$search=$_POST['search'];
$status_if_already_in_event=1;
$status_if_not_in_event=0;
$status_friended=2;

require_once 'db_connect.php';

if($search==""){
      if($result= $db->prepare("SELECT * FROM user INNER JOIN friends ON (user.user_id= friends.user_a OR user.user_id= friends.user_b) WHERE user.user_id NOT LIKE '$admin_id' AND (friends.user_a='$admin_id' OR friends.user_b='$admin_id') AND friends.status='$status_friended' ORDER BY user.name")){
        $db->beginTransaction();
        $result->bindParam(':admin_id', $admin_id);
        $result->bindValue(':status_friended', $status_friended);
        $result->execute();   
      }else{
        $response["status"]=400;
        $response["message"]="Oops. Versuchen Sie es später noch einmal.";
        echo json_encode($response);
      }
}else{
      if($result= $db->prepare("SELECT * FROM user INNER JOIN friends ON (user.user_id= friends.user_a OR user.user_id= friends.user_b) WHERE (user.name= '$search' OR user.prename='$search' OR user.email='$search') AND user.user_id NOT LIKE '$admin_id' AND (friends.user_a='$admin_id' OR friends.user_b='$admin_id') AND friends.status='$status_friended' ORDER BY user.name")){
        $db->beginTransaction();
        $result->bindParam(':admin_id', $admin_id);
        $result->bindParam(':search', $search);
        $result->bindValue(':status_friended', $status_friended);
        $result->execute();  
      }else{
        $response["status"]=400;
        $response["message"]="Oops. Versuchen Sie es später noch einmal.";
        echo json_encode($response);
      }
}

if(($result->rowCount())>0){
   $response["users"] = array();

   foreach ($result as $row) {
    $user = array();
    $user["user_id"] = $row["user_id"];
    $user_b=$row["user_id"];
    $user["name"] = $row["name"];
    $user["prename"] = $row["prename"];
    $user["email"] = $row["email"];
    
    if($checkIfAlreadyInEvent= $db->prepare("SELECT * FROM event_user WHERE user_id=:user_b AND event_id=:event_id")){
        $checkIfAlreadyInEvent->bindParam(':user_b', $user_b);
        $checkIfAlreadyInEvent->bindValue(':event_id', $event_id);
        $checkIfAlreadyInEvent->execute();  

        if(($checkIfAlreadyInEvent->rowCount())>0){
          $user["status"]=$status_if_already_in_event;
        }else{
          $user["status"]=$status_if_not_in_event;
        }
        }
    array_push($response["users"], $user);
   }

$db->commit();
$response["status"] = 200;
$response["message"] = "Organisatorenliste aktualisiert.";
echo json_encode($response);
}else{
  $response["status"] = 400;
  $response["message"] = "Suche erfolglos!";
  echo json_encode($response);
}
$db=null;
}else{
  $response["status"]=400;
  $response["message"]="Es wurden nicht alle Datensätze übertragen!";
  echo json_encode($response);
}
?>