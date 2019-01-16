<?php
require_once __DIR__ . '/../../required/db_connect.php';
$input = file_get_contents("php://input");

//Initialize error to 0
$error=0;
//Initialize an array for out JSON request
$out_json = array();

//If input is detected
if ($input){
	
	//Decode JSON input
    $json = json_decode($input, true);
	
	//Checks if there is an error in the in JSON request
    if (json_last_error() == JSON_ERROR_NONE){
		
		//Checks if user_id and func variables are in the in JSON request
        if (isset($json[0]["user_id"])){
			$in_user_id = $json[0]["user_id"];
            
			//Checks if user is in the database
            if($stmt=$mysqli->prepare("SELECT start_time, end_time, start_time_am_pm, end_time_am_pm, timestamp FROM sleep WHERE user_id = ? ORDER BY timestamp DESC LIMIT 20")){
				//Bind user_id to statement
                $stmt->bind_param('i', $in_user_id);
				//Execute statement
                $stmt->execute();
				//Store the result
                $stmt->store_result();
				//Bind user_id from database
				$stmt->bind_result($db_start_time, $db_end_time, $db_start_time_am_pm, $db_end_time_am_pm, $db_timestamp);
				//Get user_id from database
				while($stmt->fetch()){
					$row_array['start_time'] = $db_start_time;
					$row_array['end_time'] = $db_end_time;
					$row_array['start_time_am_pm'] = $db_start_time_am_pm;
					$row_array['end_time_am_pm'] = $db_end_time_am_pm;
					$row_array['timestamp'] = $db_timestamp;
					
					array_push($out_json,$row_array);
				}
				$stmt->close();
			}else {$error = 0;}
		} else {$error = 1;}
	} else {$error = 2;}
}else {$error = 3;};

$error_array['error'] = $error;
array_push($out_json, $error_array);
echo json_encode($out_json);
				