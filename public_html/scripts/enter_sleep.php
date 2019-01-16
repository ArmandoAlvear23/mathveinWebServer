<?php
require_once __DIR__ . '/../../required/db_connect.php';
$input = file_get_contents("php://input");

$error=0;
$out_json = array();

if ($input){
    $json = json_decode($input, true);
    if (json_last_error() == JSON_ERROR_NONE){
        if (isset($json["user_id"]) && isset($json["start_time"]) && isset($json["end_time"])){
            $in_user_id = $json["user_id"];
            $in_start_time = $json["start_time"];
            $in_end_time = $json["end_time"];
            $in_start_time_am_pm = $json["start_time_am_pm"];
            $in_end_time_am_pm = $json["end_time_am_pm"];
                        
            if($stmt=$mysqli->prepare("INSERT INTO sleep (user_id, start_time, end_time, start_time_am_pm, end_time_am_pm) VALUES (?,?,?,?,?)")){
				$stmt->bind_param('issii', $in_user_id, $in_start_time, $in_end_time, $in_start_time_am_pm, $in_end_time_am_pm);
                $stmt->execute();
				$stmt->close();
            } else {$error=1;}
		} else {$error=2;}
	}else {$error=3;}
}else {$error=4;}

$out_json['error'] = $error;
echo json_encode($out_json);
?>