<?php
require_once __DIR__ . '/../../required/db_connect.php';
$input = file_get_contents("php://input");

$error=0;
$type = 1;
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
			
            if($stmt=$mysqli->prepare("INSERT INTO exercises (user_id, description, intensity, start_time, start_time_am_pm, 
			end_time, end_time_am_pm) VALUES (?,?,?,?,?,?,?)")){
				$stmt->bind_param('isisisi', $in_user_id, $in_description, $in_intensity, $in_start_time, $in_start_time_am_pm,
				$in_end_time, $in_end_time_am_pm);
                $stmt->execute();
				$stmt->close();	
            } else {$error=3;} //Error in inserting into exercises
		} else {$error=4;} //No data in JSON Request
	} else {$error=5;} //Error in JSON Request
} else {$error=6;} //Error getting input

$out_json['error'] = $error;
echo json_encode($out_json);

if ($stmt=$mysqli->prepare("SELECT exercise_id FROM exercises e1
					WHERE e1.user_id = ? AND e1.timestamp = (SELECT MAX(e1.timestamp) 
					FROM exercises e2 WHERE e2.user_id = e1.user_id) 
					ORDER BY timestamp DESC LIMIT 1;")){
				$stmt->bind_param('i', $in_user_id);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($db_exercise_id);
				$stmt->fetch();
				$stmt->close();
				} else {$error=1;} //Error getting latest exercise_id from exercises
				
				if($stmt=$mysqli->prepare("INSERT INTO activities (user_id, exercise_id, type) VALUES (?,?,?)")){
				$stmt->bind_pram('iii', $in_user_id, $db_exercise_id, $type);
				$stmt->execute;
				$stmt->close;
				} else {$error=2;} //Error inserting into activities 
?>