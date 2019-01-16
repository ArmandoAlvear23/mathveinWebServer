<?php
require_once __DIR__ . '/../../required/db_connect.php';
$input = file_get_contents("php://input");

$error=0;
$out_json = array();

if ($input){
    $json = json_decode($input, true);
    if (json_last_error() == JSON_ERROR_NONE){
        if (isset($json["user_id"]) && isset($json["description"]) && isset($json["intensity"]) && isset($json["start_time"])
			&& isset($json["start_time_am_pm"]) && isset($json["end_time"]) && isset($json["end_time_am_pm"])){
			$in_user_id = $json["user_id"];
			$in_description = $json["description"];
			$in_intensity = $json["intensity"];
			$in_start_time = $json["start_time"];
			$in_start_time_am_pm = $json["start_time_am_pm"];
			$in_end_time = $json["end_time"];
			$in_end_time_am_pm = $json["end_time_am_pm"];
			
            if($stmt=$mysqli->prepare("INSERT INTO work (user_id, description, intensity, start_time, start_time_am_pm, 
			end_time, end_time_am_pm) VALUES (?,?,?,?,?,?,?)")){
				$stmt->bind_param('isisisi', $in_user_id, $in_description, $in_intensity, $in_start_time, $in_start_time_am_pm,
				$in_end_time, $in_end_time_am_pm);
                $stmt->execute();
				$stmt->close();
            } else {$error=1;} //Error in prepared statement
		} else {$error=2;} //No data in JSON Request
	} else {$error=3;} //Error in JSON Request
} else {$error=4;} //Error getting input

$out_json['error'] = $error;
echo json_encode($out_json);
?>