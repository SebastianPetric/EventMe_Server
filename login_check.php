<?php

$response= array();

if(isset($_POST['email']) && isset($_POST['password'])){

    $email = $_POST['email'];
    $password = $_POST['password'].'';

    require_once 'db_connect.php';

    $db = new DB_CONNECT();

    $result = mysql_query("SELECT * FROM user WHERE email='$email'");
    

    if(mysql_num_rows($result) > 0){
        
        $row = mysql_fetch_array($result);  
        $passwordCheck = $row["password"];
        $user_id=$row["user_id"]; 
        
        if(strcmp($password,$passwordCheck)==0){
            $response["status"] = 200;
            $response["user_id"]=$user_id;
            $response["message"] = "Login erfolgreich.";
            echo json_encode($response);
        }else{
            
            $response["status"] = 400;
            $response["message"] = "Password nicht korrekt!";
            echo json_encode($response);
        }
    }else{
        $response["status"] = 400;
        $response["message"] = "Email existiert nicht!";
        echo json_encode($response);
    }
}
else{
    $response["status"] = 400;
    $response["message"] = "Oops. Es ist ein Fehler aufgetreten!";
    echo json_encode($response);
}
?>