<?php
require_once __DIR__.'/../../required/db_connect.php';
require_once __DIR__.'/../../required/functions.php';
secure_session_start();
if(isset($_POST['email'], $_POST['password'])){
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if(login($email, $password, $mysqli)){
        header('Location: ../../protected_page.php');
    }else{
        header('Location: ../../index.php?error=1');
    }
}else {echo 'Invalid Request... null values';}
?>