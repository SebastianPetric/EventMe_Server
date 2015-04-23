<?php
 
$response= array();
 
if(isset($_POST['email']) && isset($_POST['password'])){
 
    $password = $_POST['password'];
    $email = $_POST['email'];

    require_once 'db_connect.php';
          
    if($login = $db->prepare("SELECT * FROM `user` WHERE `email` = :email")){
        $db->beginTransaction();
        $login->bindParam(':email', $email);
        $login->execute();
            if(($login->rowCount())>0){   
                while ($row = $login->fetch()) {
                    $password_check = $row["password"];
                    $user_id=$row["user_id"]; 
            if(strcmp($password,$password_check)==0){
                $db -> commit (); 
                $response["status"] = 200;
                $response["user_id"]=$user_id;
                $response["message"] = "Login erfolgreich.";
                echo json_encode($response);
            }else{
                $db -> rollBack (); 
                $response["status"] = 400;
                $response["message"] = "Password nicht korrekt!";
                echo json_encode($response);
            }
                }
            }else{
                $db -> rollBack (); 
                $response["status"] = 400;
                $response["message"] = "Email existiert nicht!";
                echo json_encode($response);
            }
    }else{
        $response["status"] = 400;
        $response["message"] = "Oops. Es ist ein Fehler aufgetreten!";
        echo json_encode($response);
    }  
    $db = null;
}else{
    $response["status"] = 400;
    $response["message"] = "Es wurden nicht alle Datensätze übertragen!";
    echo json_encode($response);
}
 
?>