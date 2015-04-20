<?php 

$response= array();

if(isset($_POST['admin_id'])&&isset($_POST['event_id'])&&isset($_POST['search'])){

require_once 'db_connect.php';
$db = new DB_CONNECT();

$admin_id=$_POST['admin_id'];
$event_id=$_POST['event_id'];
$search=mysql_real_escape_string($_POST['search']);

$status_if_already_in_event=1;
$status_if_not_in_event=0;
$status_friended=2;

if($search==""){
      $getAllFriends= mysql_query("SELECT * FROM user INNER JOIN friends ON (user.user_id= friends.user_a OR user.user_id= friends.user_b) WHERE user.user_id NOT LIKE '$admin_id' AND (friends.user_a='$admin_id' OR friends.user_b='$admin_id') AND friends.status='$status_friended' ORDER BY user.name");

}else{
      $getAllFriends= mysql_query("SELECT * FROM user INNER JOIN friends ON (user.user_id= friends.user_a OR user.user_id= friends.user_b) WHERE (user.name= '$search' OR user.prename='$search' OR user.email='$search') AND user.user_id NOT LIKE '$admin_id' AND (friends.user_a='$admin_id' OR friends.user_b='$admin_id') AND friends.status='$status_friended' ORDER BY user.name");
}

if(mysql_num_rows($getAllFriends)>0){
   
   $response["users"] = array();

    while ($row = mysql_fetch_array($getAllFriends)) {
        $user = array();
        $user["user_id"] = $row["user_id"];
        $user_b=$row["user_id"];

        $checkIfAlreadyInEvent= mysql_query("SELECT * FROM event_user WHERE user_id='$user_b' AND event_id='$event_id'");
        
        if(mysql_num_rows($checkIfAlreadyInEvent)>0){
          $user["status"]=$status_if_already_in_event;
        }else{
          $user["status"]=$status_if_not_in_event;
        }
        
        $user["name"] = $row["name"];
        $user["prename"] = $row["prename"];
        $user["email"] = $row["email"];
        array_push($response["users"], $user);
    }
    $response["status"] = 200;
    $response["message"] = "Organisatorenliste aktualisiert.";
    echo json_encode($response);
}else{
   $response["status"] = 400;
   $response["message"] = "Du hast noch keine Freunde!";
   echo json_encode($response);
}
}
?>