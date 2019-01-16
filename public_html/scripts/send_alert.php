<?php
require_once __DIR__ . '/../../required/db_connect.php';
$input = file_get_contents("php://input");

//Initialize error to 0
$error=0;
//Initialize an array for out JSON request
$out_json = array();
$contact_ids = array();
$contact_info = array();

//If input is detected
if ($input){
	
	//Decode JSON input
    $json = json_decode($input, true);
	
	//Checks if there is an error in the in JSON request
    if (json_last_error() == JSON_ERROR_NONE){
		
		//Checks if user_id and func variables are in the in JSON request
        if (isset($json["user_id"]) && isset($json["location"])){
			
			//Get user_id value from in JSON request
            $in_user_id = $json["user_id"];
			//Get func value from in JSON request
            $in_func = $json["location"];
            
			//UPDATE DATABASE: alerts
			//Prepare an UPDATE statement to update location in alerts
			if($stmt=$mysqli->prepare("UPDATE alerts SET location=? WHERE user_id = ? ORDER BY timestamp DESC LIMIT 1")){
				//Bind user_id to statement
				$stmt->bind_param('si', $location,$in_user_id);
				//Execute statement
				$stmt->execute();
				//Store the result
				$stmt->store_result();
				//Bind monitor_mode from database
				$stmt->bind_result($db_monitor_mode);
				//Get monitor_mode from database
				$stmt->fetch();
				//Close prepared statement
				$stmt->close();
				
				//Get location and vitals_history_id from alerts
				if($stmt=$mysqli->prepare("SELECT vh_id, location FROM alerts WHERE user_id=? ORDER BY timestamp DESC LIMIT 1")){
					$stmt->bind_param('i', $in_user_id);
					$stmt->execute();
					$stmt->store_result();
					$stmt->bind_result($db_vh_id, $db_location);
					$stmt->fetch();
					$stmt->close();
					
					//Get vitals info from vitals_history using vitals_history_id
					if($stmt=$mysqli->prepare("SELECT systolic, diastolic, blood_glucose, FROM vitals_history WHERE vh_id=? ORDER BY timestamp DESC LIMIT 1")){
						$stmt->bind_param('i', $db_vh_id);
						$stmt->execute();
						$stmt->store_result();
						$stmt->bind_result($db_systolic, $db_diastolic, $db_blood_glucose);
						$stmt->fetch();
						$stmt->close();
						
						//Get first name and last name of patient from users using user_id
						if($stmt=$mysqli->prepare("SELECT fname, lname  FROM users WHERE user_id=? LIMIT 1")){
							$stmt->bind_param('i', $in_user_id);
							$stmt->execute();
							$stmt->store_result();
							$stmt->bind_result($fname, $lname);
							$stmt->fetch();
							$stmt->close();
							
							//Get array of all relationships with user_error
							if($stmt=$mysqli->prepare("SELECT contact_id  FROM relationships WHERE user_id=? LIMIT 5")){
								$stmt->bind_param('i', $in_user_id);
								$stmt->execute();
								$stmt->store_result();
								$stmt->bind_result($db_contact_id);
								while($stmt->fetch()){
									$row_id['contact_id'] = $db_contact_id;
									array_push($contact_ids, $row_id);
								}
								$stmt->close();
								
								//Get all emails from cotacts using contact_id array
								if($stmt=$mysqli->prepare("SELECT email, fname, lname  FROM contacts WHERE contact_id=? LIMIT 1")){
									$length = count($contact_ids);
									for($i=0; $i<$length; $i++){
										$stmt->bind_param('i', $contact_ids[$i][0]);
										$stmt->execute();
										$stmt->store_result();
										$stmt->bind_result($db_contact_email, $db_contact_fname, $db_contact_lname);
										while($stmt->fetch()){
											$row_info['contact_email'] = $db_contact_email;
											$row_info['contact_fname'] = $db_contact_fname;
											$row_info['contact_lname'] = $db_contact_lname;
											array_push($contact_info, $row_info);
										}
									}
									$stmt->close();
									
									//send Alert Email
									$contact_info_length = count($contact_info);
									$subject = "BPM Threshold Alert";
								
									for($i=0; $i<$contact_info_length; $i++){
										$send_email = $contact_info[i][0];
										$send_fname = $contact_info[i][1];
										$send_lname = $contact_info[i][2];
										
										$message = "BPM threshold alert has been triggered for " + $send_fname + " " + $send_lname + ".";
										mail($send_email, $subject, $message, "");
										
									}	
								}else{$error=1;}
							}else{$error=1;}
						}else{$error=1;}
					}else{$error=1;}
				}else{$error=1;}
			}else {$error=2;} //Error in prepared statement
		} else {$error=8;} //Error getting user_id, func values from in JSON request
	}else {$error=9;} //Error in JSON request
}else {$error=10;} //Error in input

$out_json['error'] = $error;
echo json_encode($out_json);
?>