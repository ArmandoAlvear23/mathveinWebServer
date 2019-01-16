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
        if (isset($json["user_id"]) && isset($json["func"])){
			
			//Get user_id value from in JSON request
            $in_user_id = $json["user_id"];
			//Get func value from in JSON request
            $in_func = $json["func"];
            
			//Checks if user is in the database
            if($stmt=$mysqli->prepare("SELECT user_id FROM monitoring_mode WHERE user_id = ? LIMIT 1")){
				//Bind user_id to statement
                $stmt->bind_param('i', $in_user_id);
				//Execute statement
                $stmt->execute();
				//Store the result
                $stmt->store_result();
				//Bind user_id from database
				$stmt->bind_result($db_user_id);
				//Get user_id from database
				$stmt->fetch();
				
				//If user exists
                if($stmt->num_rows == 1){	
					
					//Close prepared statement
					$stmt->close();
					
					//Function for checking monitor_mode value in Database
					if($in_func == 1){
							
						//GET FROM DATABASE
						//Prepare a SELECT statement to get monitor_mode from database
						if($stmt=$mysqli->prepare("SELECT monitor_mode FROM monitoring_mode WHERE user_id = ? LIMIT 1")){
							
							//Bind user_id to statement
							$stmt->bind_param('i', $in_user_id);
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
							
						}else {$error=1;} //Error in prepared statement
						
						//INSERT INTO OUT JSON REQUEST
						//Insert monitor_mode value from database into out JSON request
						$out_json['monitor_mode'] = $db_monitor_mode;
					}
					
					//Function for turning ON monitor mode
					else if($in_func == 2){
						
						//GET FROM DATABASE
						//Prepare a SELECT statement to get monitor_mode from database
						if($stmt=$mysqli->prepare("SELECT monitor_mode, bpm1, bpm2, bpm3, bpm_counter, no_input_flag FROM monitoring_mode WHERE user_id = ? LIMIT 1")){
							//Bind user_id to statement
							$stmt->bind_param('i', $in_user_id);
							//Execute statement
							$stmt->execute();
							//Store the result
							$stmt->store_result();
							//Bind monitor_mode from database
							$stmt->bind_result($db_monitor_mode, $db_bpm1, $db_bpm2, $db_bpm3, $db_bpm_counter, $db_no_input_flag);
							//Get monitor_mode from database
							$stmt->fetch();
							//Close prepared statement
							$stmt->close();
							
						}else {$error=2;} //Error in prepared statement
						
						//INSERT INTO OUT JSON REQUEST
						//Insert monitor_mode value from database into out JSON request
						$out_json['monitor_mode'] = $db_monitor_mode;
						$out_json['bpm1'] = $db_bpm1;
						$out_json['bpm2'] = $db_bpm2;
						$out_json['bpm3'] = $db_bpm3;
						$out_json['bpm_counter'] = $db_bpm_counter;
						$out_json['no_input_flag'] = $db_no_input_flag;
					}
					
					else if ($in_func == 3){
						if (isset($json["bpm_counter"]) && isset($json["bpm1"]) && isset($json["bpm_average"])){
							$in_bpm_counter = $json["bpm_counter"];
							$in_bpm1 = $json["bpm1"];
							$in_bpmAvg = $json["bpm_average"];
							
							if ($stmt=$mysqli->prepare("UPDATE monitoring_mode SET bpm_counter=?, bpm1=?, bpm_average=? WHERE user_id = ?")){
								$stmt->bind_param('iiii', $in_bpm_counter, $in_bpm1, $in_bpmAvg, $in_user_id);
								$stmt->execute();
								$stmt->close();
							}else{$error=3;} //Error in updating prepared statement 
							
							if($stmt=$mysqli->prepare("SELECT bpm1, bpm2, bpm3, bpm_average FROM monitoring_mode WHERE user_id=?")){
								$stmt->bind_param('i',$in_user_id);
								$stmt->execute();
								$stmt->store_result();
								$stmt->bind_result($db_bpm1, $db_bpm2, $db_bpm3, $db_bpm_average);
								$stmt->fetch();
								$stmt->close();
							}else{$error=4;}//Error fetching data from database
							
							if($db_bpm1 == 0 && $db_bpm2 == 0 && $db_bpm3 == 0){
								$no_input_flag = 0;
								if($stmt=$mysqli->prepare("UPDATE monitoring_mode SET no_input_flag=? WHERE user_id=?")){
									$stmt->bind_param('ii', $no_input_flag, $in_user_id);
									$stmt->execute();
									$stmt->close();
								}else{$error=5;} //Error in updating prepared statement								
							}
							
							if($stmt=$mysqli->prepare("SELECT bpm_low_limit, bpm_high_limit FROM settings WHERE user_id=?")){
								$stmt->bind_param('i', $in_user_id);
								$stmt->execute();
								$stmt->store_result();
								$stmt->bind_result($db_low_limit, $db_high_limit);
								$stmt->fetch();
								$stmt->close();
							}
							$bpm_flag = 1;
							if($db_bpm_average < $db_low_limit){
								if($stmt=$mysqli->prepare("INSERT INTO flags (user_id, bpm_lower_flag) VALUES (?,?)")){
									$stmt->bind_param('ii', $in_user_id, $bpm_flag);
									$stmt->execute();
									$stmt->close();
								}
							}
							else if ($db_bpm_average > $db_high_limit){
								if($stmt=$mysqli->prepare("INSERT INTO flags (user_id, bpm_upper_flag) VALUES (?,?)")){
									$stmt->bind_param('ii', $in_user_id, $bpm_flag);
									$stmt->execute();
									$stmt->close();
								}
							}
							
						}else{$error=6;} //Error getting data from JSON Request
					}
					
					else if ($in_func == 4){
						
						if (isset($json["bpm_counter"]) && isset($json["bpm2"]) && isset($json["bpm_average"])){
							$in_bpm_counter = $json["bpm_counter"];
							$in_bpm2 = $json["bpm2"];
							$in_bpmAvg = $json["bpm_average"];
							
							if ($stmt=$mysqli->prepare("UPDATE monitoring_mode SET bpm_counter=?, bpm2=?, bpm_average=? WHERE user_id = ?")){
								$stmt->bind_param('iiii', $in_bpm_counter, $in_bpm2, $in_bpmAvg, $in_user_id);
								$stmt->execute();
								$stmt->close();
							}else{$error=7;} //Error in updating prepared statement 
							
							if($stmt=$mysqli->prepare("SELECT bpm1, bpm2, bpm3, bpm_average FROM monitoring_mode WHERE user_id=?")){
								$stmt->bind_param('i',$in_user_id);
								$stmt->execute();
								$stmt->store_result();
								$stmt->bind_result($db_bpm1, $db_bpm2, $db_bpm3, $db_bpm_average);
								$stmt->fetch();
								$stmt->close();
							}else{$error=8;}//Error fetching data from database
							
							if($db_bpm1 == 0 && $db_bpm2 == 0 && $db_bpm3 == 0){
								$no_input_flag = 0;
								if($stmt=$mysqli->prepare("UPDATE monitoring_mode SET no_input_flag=? WHERE user_id=?")){
									$stmt->bind_param('ii', $no_input_flag, $in_user_id);
									$stmt->execute();
									$stmt->close();
								}else{$error=9;} //Error in updating prepared statement
							}
							
							if($stmt=$mysqli->prepare("SELECT bpm_low_limit, bpm_high_limit FROM settings WHERE user_id=?")){
								$stmt->bind_param('i', $in_user_id);
								$stmt->execute();
								$stmt->store_result();
								$stmt->bind_result($db_low_limit, $db_high_limit);
								$stmt->fetch();
								$stmt->close();
							}
							$bpm_flag = 1;
							if($db_bpm_average < $db_low_limit){
								if($stmt=$mysqli->prepare("INSERT INTO flags (user_id, bpm_lower_flag) VALUES (?,?)")){
									$stmt->bind_param('ii', $in_user_id, $bpm_flag);
									$stmt->execute();
									$stmt->close();
								}
							}
							else if ($db_bpm_average > $db_high_limit){
								if($stmt=$mysqli->prepare("INSERT INTO flags (user_id, bpm_upper_flag) VALUES (?,?)")){
									$stmt->bind_param('ii', $in_user_id, $bpm_flag);
									$stmt->execute();
									$stmt->close();
								}
							}
							
						}else{$error=10;} //Error getting data from JSON Request
						
					}
					
					else if ($in_func == 5){
						
						if (isset($json["bpm_counter"]) && isset($json["bpm3"]) && isset($json["bpm_average"])){
							$in_bpm_counter = $json["bpm_counter"];
							$in_bpm3 = $json["bpm3"];
							$in_bpmAvg = $json["bpm_average"];
							
							if ($stmt=$mysqli->prepare("UPDATE monitoring_mode SET bpm_counter=?, bpm3=?, bpm_average=? WHERE user_id = ?")){
								$stmt->bind_param('iiii', $in_bpm_counter, $in_bpm3, $in_bpmAvg, $in_user_id);
								$stmt->execute();
								$stmt->close();
							}else{$error=11;} //Error in updating prepared statement 
							
							if($stmt=$mysqli->prepare("SELECT bpm1, bpm2, bpm3, bpm_average FROM monitoring_mode WHERE user_id=?")){
								$stmt->bind_param('i',$in_user_id);
								$stmt->execute();
								$stmt->store_result();
								$stmt->bind_result($db_bpm1, $db_bpm2, $db_bpm3, $db_bpm_average);
								$stmt->fetch();
								$stmt->close();
							}else{$error=12;}//Error fetching data from database
							
							if($db_bpm1 == 0 && $db_bpm2 == 0 && $db_bpm3 == 0){
								$no_input_flag = 0;
								if($stmt=$mysqli->prepare("UPDATE monitoring_mode SET no_input_flag=? WHERE user_id=?")){
									$stmt->bind_param('ii', $no_input_flag, $in_user_id);
									$stmt->execute();
									$stmt->close();
								}else{$error=13;} //Error in updating prepared statement
							}
							
							if($stmt=$mysqli->prepare("SELECT bpm_low_limit, bpm_high_limit FROM settings WHERE user_id=?")){
								$stmt->bind_param('i', $in_user_id);
								$stmt->execute();
								$stmt->store_result();
								$stmt->bind_result($db_low_limit, $db_high_limit);
								$stmt->fetch();
								$stmt->close();
							}
							$bpm_flag = 1;
							if($db_bpm_average < $db_low_limit){
								if($stmt=$mysqli->prepare("INSERT INTO flags (user_id, bpm_lower_flag) VALUES (?,?)")){
									$stmt->bind_param('ii', $in_user_id, $bpm_flag);
									$stmt->execute();
									$stmt->close();
								}
							}
							else if ($db_bpm_average > $db_high_limit){
								if($stmt=$mysqli->prepare("INSERT INTO flags (user_id, bpm_upper_flag) VALUES (?,?)")){
									$stmt->bind_param('ii', $in_user_id, $bpm_flag);
									$stmt->execute();
									$stmt->close();
								}
							}
						}else{$error=14;} //Error getting data from JSON Request
					}
				} else {$error=15;} //Error getting user_id, func values from in JSON request
			}else {$error=16;} //Error in JSON request
		}else {$error=17;} //Error in input
	}else{$error=18;}
}

$out_json['error'] = $error;
echo json_encode($out_json);
?>