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
						
						//Checks if monitor_mode is in the in JSON request
						if(isset($json["monitor_mode"])){
							
							//Get monitor_mode value from in JSON request
							$in_monitor_mode = $json["monitor_mode"];
							
							/***UPDATE DATABASE***/
							//Prepare an UPDATE statement to update values in database
							if($stmt=$mysqli->prepare("UPDATE monitoring_mode SET monitor_mode=? WHERE user_id = ?")){
								$stmt->bind_param('ii', $in_monitor_mode, $in_user_id);
								$stmt->execute();
								$stmt->close();
							}else {$error=2;}
						
							/***GET FROM DATABASE**/
							//Prepare a SELECT statement to get monitor_mode, bpm_average, no_input_flag
							if($stmt=$mysqli->prepare("SELECT bpm_average, no_input_flag, monitor_mode FROM monitoring_mode WHERE user_id = ? LIMIT 1")){
								
								//Bind user_id to statement
								$stmt->bind_param('i', $in_user_id);
								//Execute statement
								$stmt->execute();
								//Store the result
								$stmt->store_result();
								//Bind monitor_mode, bpm_average, no_input_flag from database
								$stmt->bind_result($db_bpm_average, $db_no_input_flag, $db_monitor_mode);
								//Get monitor_mode, bpm_average. no_input_flag
								$stmt->fetch();
								//Close prepared statement
								$stmt->close();
								
							}else {$error=2;} //Error in prepared statement
						}else {$error=2;} //Error getting monitor_mode value from in JSON request
						
						/***INSERT INTO OUT JSON REQUEST***/
						//Insert bpm_average value from database into out JSON request
						$out_json['bpm_average'] = $db_bpm_average;
						//Insert no_input_flag value from database into out JSON request
						$out_json['no_input_flag'] = $db_no_input_flag;
						$out_json['monitor_mode'] = $db_monitor_mode;
					}
					
					//Function for turning OFF monitor mode
					else if ($in_func == 3){
						
						//Checks if monitor_mode is in the in JSON request
						if (isset($json["monitor_mode"])){
							
							//Get monitor_mode value from in JSON request
							$in_monitor_mode = $json["monitor_mode"];
							
							//Initialize variables for out JSON request
							$bpm1 = 0;
							$bpm2 = 0;
							$bpm3 = 0;
							$bpm_average = 0;
							$bpm_counter = 1;
							$no_input_flag = 0;
							
							/***UPDATE DATABASE**/
							//Prepare an UPDATE statement to update values in database
							if ($stmt=$mysqli->prepare("UPDATE monitoring_mode SET monitor_mode=?,bpm1=?,bpm2=?,bpm3=?,bpm_average=?,bpm_counter=?,no_input_flag=? WHERE user_id = ?")){
								
								//Bind variables to prepared statement
								$stmt->bind_param('iiiiiiii',$in_monitor_mode,$bpm1,$bpm2,$bpm3,$bpm_average,$bpm_counter,$no_input_flag,$in_user_id);
								//Execute prepared statement
								$stmt->execute();
								//Close prepared statement
								$stmt->close();
								
							}else {$error=3;} //Error in the UPDATE statement
						}else {$error=3;} //Error getting monitor_mode value from in JSON request
					}
					
					//Function to send OUT monitor_mode, bpm_average, and no_input_flag to App
					else if($in_func == 4){
						
						/***GET FROM DATABASE**/
						//Prepare a SELECT statement to get monitor_mode, bpm_average, no_input_flag
						if ($stmt=$mysqli->prepare("SELECT bpm_average, no_input_flag, monitor_mode, alert_flag FROM monitoring_mode WHERE user_id = ? LIMIT 1")){
							
							//Bind user_id to statement
							$stmt->bind_param('i', $in_user_id);
							//Execute statement
							$stmt->execute();
							//Store the result
							$stmt->store_result();
							//Bind monitor_mode, bpm_average, no_input_flag from database
							$stmt->bind_result($db_bpm_average, $db_no_input_flag, $db_monitor_mode, $db_alert_flag);
							//Get monitor_mode, bpm_average, no_input_flag, alert_flag
							$stmt->fetch();
							//Close prepared statement
							$stmt->close();
							
						}else {$error=4;} //Error in prepared statement
						
						/***INSERT INTO OUT JSON REQUEST***/
						//Insert bpm_average value from database into out JSON request
						$out_json['bpm_average'] = $db_bpm_average;
						//Insert no_input_flag value from database into out JSON request
						$out_json['no_input_flag'] = $db_no_input_flag;
						$out_json['monitor_mode'] = $db_monitor_mode;
						$out_json['alert_flag'] = $db_alert_flag;
					}
					else {$error=4;} //Error in func value from in JSON request
				}
				
				//If user not found in database
				else if($stmt->num_rows == 0){
					
					//Close prepared statement
					$stmt->close();
					
					//GET FROM DATABASE
					//Prepare a SELECT statement to get monitor_mode from database
					if($stmt=$mysqli->prepare("INSERT INTO monitoring_mode(user_id) VALUES(?)")){
						
						//Bind user_id to statement
						$stmt->bind_param('i', $in_user_id);
						//Execute statement
						$stmt->execute();	
						//Close prepared statement
						$stmt->close();
						
					}else {$error=1;} //Error in prepared statement
				}else {$error=6;} //Error in user search	
			} else {$error=7;} //Error in prepared statement searching for user	
		} else {$error=8;} //Error getting user_id, func values from in JSON request
	}else {$error=9;} //Error in JSON request
}else {$error=10;} //Error in input

$out_json['error'] = $error;
echo json_encode($out_json);
?>