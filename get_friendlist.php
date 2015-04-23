<?php 

$response= array();

if(isset($_POST['admin_id'])&&isset($_POST['search'])){

require_once 'db_connect.php';
$db = new DB_CONNECT();

$admin_id=$_POST['admin_id'];
$search=mysql_real_escape_string($_POST['search']);

$status_friended=2;
$status_open=0;
$status_have_to_accept=3;

if($search==""){
      $getAllFriends= mysql_query("SELECT * FROM user INNER JOIN friends ON (user.user_id= friends.user_a OR user.user_id= friends.user_b) WHERE user.user_id NOT LIKE '$admin_id' AND (friends.user_a='$admin_id' OR friends.user_b='$admin_id') AND (friends.status='$status_friended' OR friends.status='$status_open') ORDER BY friends.status");
}else{
  $getAllFriends= mysql_query("SELECT * FROM user INNER JOIN friends ON (user.user_id= friends.user_a OR user.user_id= friends.user_b) WHERE (user.name= '$search' OR user.prename='$search' OR user.email='$search') AND user.user_id NOT LIKE '$admin_id' AND (friends.user_a='$admin_id' OR friends.user_b='$admin_id') AND (friends.status='$status_friended' OR friends.status='$status_open') ORDER BY friends.status");  
}
$response["users"] = array();
if(mysql_num_rows($getAllFriends)>0){
    while ($row = mysql_fetch_array($getAllFriends)) {
        $user = array();
        $user["user_id"] = $row["user_id"];
        $user_b=$row["user_id"];
        $getStatusFriended= mysql_query("SELECT * FROM friends WHERE (user_a='$admin_id' AND user_b='$user_b') OR (user_a='$user_b' AND user_b='$admin_id') AND status='$status_friended'");
        if(mysql_num_rows($getStatusFriended)>0){
            $user_status=mysql_fetch_array($getStatusFriended);
            $user["status"]=$user_status['status'];
          }else{
              $getStatusHaveToAccept= mysql_query("SELECT * FROM friends WHERE user_a='$user_b' AND user_b='$admin_id' AND status='$status_open'");
              if(mysql_num_rows($getStatusHaveToAccept)>0){
                $user["status"]=$status_have_to_accept;
              }else{
                $getStatusOpen= mysql_query("SELECT * FROM friends WHERE user_a='$admin_id' AND user_b='$user_b' AND status='$status_open'");
                if(mysql_num_rows($getStatusOpen)>0){
                  $user_status=mysql_fetch_array($getStatusOpen);
                  $user["status"]=$user_status['status'];
                }
              }
          }
        $user["name"] = $row["name"];
        $user["prename"] = $row["prename"];
        $user["email"] = $row["email"];
        array_push($response["users"], $user);
    }
    $response["status"] = 200;
    $response["message"] = "Freundesliste aktualisiert.";
    echo json_encode($response);
}else{
   $response["status"] = 400;
   $response["message"] = "Keine Freunde vorhanden!";
   echo json_encode($response);
}
}else{
    $response["status"] = 400;
   $response["message"] = "Oops. Fehlende Datensätze.";
   echo json_encode($response);
}
?>
