<?php
require_once __DIR__ . '/../../required/db_connect.php';
$input = file_get_contents("php://input");
$error=0;
$out_json = array();
$out_json['success'] = 1;
$out_json['confirm'] = 0;

if ($input){
    $json = json_decode($input, true);
    if (json_last_error() == JSON_ERROR_NONE){
        if (isset($json["email"]) && isset($json["pass"])){
            $in_email = $json["email"];
            $in_pass = $json["pass"];
            if($stmt=$mysqli->prepare("SELECT pass FROM login WHERE email = ? LIMIT 1")){
                $stmt->bind_param('s', $in_email);
                $stmt->execute();
                $stmt->store-result();
                $stmt->bind_result($db_pass);
                $stmt->fetch();
                
                if($stmt->num_rows == 1){
                    if (password_verify($in_pass, $db_pass)){
                        $stmt->close();
                        $out_json['confirm'] = 1;
                    }else {$error=1;}#pass doesn't match
                }else {$error=2;}#email not found
            }else {$error=3;} #error in prepare stmt
        }else {$error=4;}#json data is empty (isset)
    }else {$error=5;} #error at json
}else{$error=6;}#error at input
if($error){
    $out_json['success']=0;
}
$out_json['error'] = $error;
echo json_encode($out_json);
?>