<?php

$response = array();


if (isset($_POST['name']) && isset($_POST['prename']) && isset($_POST['email'])&& isset($_POST['password'])) {

    $name = mysql_real_escape_string($_POST['name']);
    $prename = mysql_real_escape_string($_POST['prename']);
    $email = mysql_real_escape_string($_POST['email']);
    $password =mysql_real_escape_string($_POST['password']);

    require_once 'db_connect.php';

    $db = new DB_CONNECT();

    $checkIfEmailAlreadyRegistered= mysql_query("SELECT * FROM user WHERE email='$email'");

    if(mysql_num_rows($checkIfEmailAlreadyRegistered) == 0) {

        $registration = mysql_query("INSERT INTO user(name, prename, email,password) VALUES('$name', '$prename', '$email', '$password')");
        if ($registration) {
            $response["status"] = 200;
            $response["message"] = "Erfolgreich registriert!";
            echo json_encode($response);
        } else {
            $response["status"] = 400;
            $response["message"] = "Oops! Datenbankverbindung fehlgeschlagen.";
            echo json_encode($response);
        }
    }else{
        $response["status"] = 400;
        $response["message"] = "Email Adresse schon registriert!";
        echo json_encode($response);
    }
} else {
    $response["status"] = 400;
    $response["message"] = "Es ist ein Fehler aufgetreten!";
    echo json_encode($response);
}
?>