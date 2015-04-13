<?php 

$response= array();

if(isset($_POST['search'])&&isset($_POST['user_id'])){

$search=mysql_real_escape_string($_POST['search']);
$user_id=$_POST['user_id'];
$status_open=0;
$status_have_to_accept=3;
$status_unfriended=1;
$status_friended=2;

require_once 'db_connect.php';
$db = new DB_CONNECT();

if($search==""){
      $result= mysql_query("SELECT * FROM user WHERE user_id NOT LIKE '$user_id'");
}else{
      $result= mysql_query("SELECT * FROM user WHERE (name= '$search' OR prename='$search' OR email='$search') AND user_id NOT LIKE '$user_id'");
}
if(mysql_num_rows($result)>0){
   
   $response["users"] = array();

    while ($row = mysql_fetch_array($result)) {
        $user = array();
        $user["user_id"] = $row["user_id"];
        $userb_id=$row["user_id"];
        $checkStatus= mysql_query("SELECT * FROM friends WHERE ((user_a,user_b)= ('$user_id','$userb_id') OR (user_a,user_b)= ('$userb_id','$user_id'))");
        if(mysql_num_rows($checkStatus)>0){
          $checkStatusIfOpen= mysql_query("SELECT * FROM friends WHERE ((user_a,user_b)= ('$user_id','$userb_id') OR (user_a,user_b)= ('$userb_id','$user_id')) AND status='$status_open'");
            if(mysql_num_rows($checkStatusIfOpen)>0){
              $checkIfYouHaveToAccept= mysql_query("SELECT * FROM friends WHERE (user_a,user_b)= ('$userb_id','$user_id') AND status='$status_open'");
              if(mysql_num_rows($checkIfYouHaveToAccept)>0){
                  $user["status_friend"]=$status_have_to_accept; 
              }else{
                $row_status = mysql_fetch_array($checkStatus);
                $user["status_friend"]=$row_status["status"]; 
              }
            }else{
              $row_status = mysql_fetch_array($checkStatus);
              $user["status_friend"]=intval($row_status["status"]); 
            }
        }else{
          $user["status_friend"]=$status_unfriended;
        }
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