<?php
require_once __DIR__ . '/../required/db_connect.php';
$email = "email@email.com";
$password = "pass";
$datum = new DateTime();
$fname = "first";
$lname = "last";
$dob = "01/01/2018";
$createTime = $datum->format('Y-m-d H:i:s');

if($stmt = $mysqli->prepare("INSERT INTO login(email, pass, timestamp) VALUES (?,?,?)")){
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt->bind_param('sss',$email, $hashedPassword, $createTime);
    $stmt->execute();
}
$stmt->close();
if($stmt=$mysqli->prepare("INSERT INTO users(fname,lname,dob, create_date) VALUES (?,?,?,?)")){
    $stmt->bind_param('ssss',$fname,$lname,$dob,$createTime);
    $stmt->execute();
}
$stmt->close();
?>