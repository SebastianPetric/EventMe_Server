<?php 

$response= array();

if(isset($_POST['search'])&&isset($_POST['admin_id'])){


require_once 'db_connect.php';
$db = new DB_CONNECT();

$search=mysql_real_escape_string($_POST['search']);
$admin_id=$_POST['admin_id'];
$status_open=0;
$status_have_to_accept=3;
$status_unfriended=1;
$status_friended=2;

if($search==""){
      $result= mysql_query("SELECT * FROM user WHERE user_id NOT LIKE '$admin_id'");
}else{
      $result= mysql_query("SELECT * FROM user WHERE (name= '$search' OR prename='$search' OR email='$search') AND user_id NOT LIKE '$admin_id'");
}
if(mysql_num_rows($result)>0){
   
   $response["users"] = array();

    while ($row = mysql_fetch_array($result)) {
        $user = array();
        $user["user_id"] = $row["user_id"];
        $userb_id=$row["user_id"];
        $checkStatus= mysql_query("SELECT * FROM friends WHERE ((user_a,user_b)= ('$admin_id','$userb_id') OR (user_a,user_b)= ('$userb_id','$admin_id'))");
        if(mysql_num_rows($checkStatus)>0){
          $checkStatusIfOpen= mysql_query("SELECT * FROM friends WHERE ((user_a,user_b)= ('$admin_id','$userb_id') OR (user_a,user_b)= ('$userb_id','$admin_id')) AND status='$status_open'");
            if(mysql_num_rows($checkStatusIfOpen)>0){
              $checkIfYouHaveToAccept= mysql_query("SELECT * FROM friends WHERE (user_a,user_b)= ('$userb_id','$admin_id') AND status='$status_open'");
              if(mysql_num_rows($checkIfYouHaveToAccept)>0){
                  $user["status"]=$status_have_to_accept; 
              }else{
                $row_status = mysql_fetch_array($checkStatus);
                $user["status"]=$row_status["status"]; 
              }
            }else{
              $row_status = mysql_fetch_array($checkStatus);
              $user["status"]=intval($row_status["status"]); 
            }
        }else{
          $user["status"]=$status_unfriended;
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
   $response["message"] = "Suche erfolglos!";
   echo json_encode($response);
}
}
?>