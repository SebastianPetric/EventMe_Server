<?php

$response = array();

if (isset($_POST['name']) && isset($_POST['prename']) && isset($_POST['email'])&& isset($_POST['password'])) {

    $name = $_POST['name'];
    $prename = $_POST['prename'];
    $email = $_POST['email'];
    $password =$_POST['password'];

    require_once 'db_connect.php';
    
    if($email_already_registered = $db->prepare("SELECT * FROM user WHERE email = :email")){
        $db->beginTransaction();
        $email_already_registered->bindParam(':email', $email);
        $email_already_registered->execute();

         if(($email_already_registered->rowCount())==0){

            if($registration = $db->prepare("INSERT INTO user(name, prename, email,password) VALUES(:name, :prename, :email, :password)")){
                $registration->bindParam(':name', $name);
                $registration->bindParam(':prename', $prename);
                $registration->bindParam(':email', $email);
                $registration->bindParam(':password', $password);
                $registration->execute();
        if ($registration) {
            $db -> commit (); 
            $response["status"] = 200;
            $response["message"] = "Erfolgreich registriert!";
            echo json_encode($response);
        } else {
             $db -> rollBack ();
            $response["status"] = 400;
            $response["message"] = "Oops! Datenbankverbindung fehlgeschlagen.";
            echo json_encode($response);
        }
            }else{
                $db -> rollBack ();
                $response["status"] = 400;
                $response["message"] = "Oops! Datenbankverbindung fehlgeschlagen.";
                echo json_encode($response);
            }
         }else{
            $db -> rollBack ();
            $response["status"] = 400;
            $response["message"] = "Email Adresse schon registriert!";
            echo json_encode($response);
         }
         
    }else{
        $response["status"] = 400;
        $response["message"] = "Oops. Es ist ein Fehler aufgetreten!";
        echo json_encode($response);
    }    
    $db = null;  
} else {
    $response["status"] = 400;
    $response["message"] = "Es wurden nicht alle Datensätze übertragen!";
    echo json_encode($response);
}
?>