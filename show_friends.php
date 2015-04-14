<?php 

$response= array();

if(isset($_POST['user_id'])&&isset($_POST['event_id'])){

$user_id=$_POST['user_id'];
$event_id=$_POST['event_id'];

$status_if_added=1;
$status_friended=2;

require_once 'db_connect.php';
$db = new DB_CONNECT();

      $result= mysql_query("SELECT * FROM user INNER JOIN friends ON user.user_id= friends.user_a OR user.user_id= friends.user_b WHERE user.user_id NOT LIKE '$user_id' AND friends.status=$status_friended");

if(mysql_num_rows($result)>0){
   
   $response["users"] = array();

    while ($row = mysql_fetch_array($result)) {
        $user = array();
        $user["user_id"] = $row["user_id"];
        $userb_id=$row["user_id"];
        $user["status_if_added"]=$status_if_added;
        $user["name"] = $row["name"];
        $user["prename"] = $row["prename"];
        $user["email"] = $row["email"];
        array_push($response["users"], $user);
    }
    $response["status"] = 200;
    $response["message"] = "Liste aktualisiert.";
    echo json_encode($response);
}else{
   $response["status"] = 400;
   $response["message"] = "Es gibt noch keine anderen registrierten User!";
   echo json_encode($response);
}
}
?>