<?php
require_once __DIR__ . '/../../required/db_connect.php';
$input = file_get_contents("php://input");

$error=0;
$out_json = array();

if ($input){
    $json = json_decode($input, true);
    if (json_last_error() == JSON_ERROR_NONE){
        if (isset($json["user_id"]) && (isset($json["appetizer"]) || isset($json["main"]) || isset($json["dessert"]) || isset($json["snack"]) || isset($json["drink"]))){
			$in_user_id = $json["user_id"];
			
			if(isset($json["appetizer"])){
				$in_appetizer = $json["appetizer"];
			}
			else{
				$in_appetizer = "null";
			}
			if(isset($json["main"])){
				$in_main = $json["main"];
			}
			else{
				$in_main = "null";
			}
			if(isset($json["dessert"])){
				$in_dessert = $json["dessert"];
			}
			else{
				$in_dessert = "null";
			}
			if(isset($json["snack"])){
				$in_snack = $json["snack"];
			}
			else{
				$in_snack = "null";
			}
			if(isset($json["drink"])){
				$in_drink = $json["drink"];
			}  
			else{
				$in_drink = "null";
			}
            if($stmt=$mysqli->prepare("INSERT INTO meals (user_id, appetizer, main, dessert, snack, drink) VALUES (?,?,?,?,?,?)")){
				$stmt->bind_param('isssss', $in_user_id, $in_appetizer, $in_main, $in_dessert, $in_snack, $in_drink);
                $stmt->execute();
				$stmt->close();
            } else {$error=1;} //Error in prepared statement
		} else {$error=2;} //No data in JSON Request
	}else {$error=3;} //Error in JSON Request
}else {$error=4;} //Error getting input

$out_json['error'] = $error;
echo json_encode($out_json);
?>