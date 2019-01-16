<?php
require_once __DIR__ . '/../../required/db_connect.php';
$input = file_get_contents("php://input");

$error=0;
$out_json = array();
$out_json['success'] = 1;

if ($input){
    $json = json_decode($input, true);
    if (json_last_error() == JSON_ERROR_NONE){
        if (isset($json["user_id"]) && isset($json["systolic"]) && isset($json["diastolic"]) && isset($json["blood_glucose"])){
            $in_user_id = $json["user_id"];
            $in_systolic = $json["systolic"];
            $in_diastolic = $json["diastolic"];
            $in_blood_glucose = $json["blood_glucose"];
            
            if($stmt=$mysqli->prepare("SELECT user_id FROM current_vitals WHERE user_id = ? LIMIT 1")){
                $stmt->bind_param('i', $in_user_id);
                $stmt->execute();
                $stmt->store_result();
				$stmt->bind_result($db_user_id);
				$stmt->fetch();
				
                if($stmt->num_rows == 1){
					$stmt->close();
					if($stmt=$mysqli->prepare("UPDATE current_vitals SET systolic=?, diastolic=?, blood_glucose=? WHERE user_id = ?")){
                        $stmt->bind_param('iidi',$in_systolic,$in_diastolic,$in_blood_glucose, $db_user_id);
                        $stmt->execute();
						$stmt->close();
                    } else {$error=8;}
					if($stmt=$mysqli->prepare("INSERT INTO vitals_history (user_id, systolic, diastolic, blood_glucose) VALUES (?,?,?,?)")){
							$stmt->bind_param('iiid', $in_user_id, $in_systolic, $in_diastolic, $in_blood_glucose);
							$stmt->execute();
							$stmt->close();
					} else {$error=9;}
                }
				else if($stmt->num_rows == 0){
					$stmt->close();
					if($stmt=$mysqli->prepare("INSERT INTO current_vitals (user_id, systolic, diastolic, blood_glucose) VALUES (?,?,?,?)")){
							$stmt->bind_param('iiid', $in_user_id, $in_systolic, $in_diastolic, $in_blood_glucose);
							$stmt->execute();
							$stmt->close();
					}else {$error=1;}
					
					if($stmt=$mysqli->prepare("INSERT INTO vitals_history (user_id, systolic, diastolic, blood_glucose) VALUES (?,?,?,?)")){
							$stmt->bind_param('iiid', $in_user_id, $in_systolic, $in_diastolic, $in_blood_glucose);
							$stmt->execute();
							$stmt->close();
					} else {$error=10;}
					
				} else {$error=2;}
				
				
            } else {$error=3;}
		} else {$error=4;}
	}else {$error=5;}
}else {$error=6;}

if ($error){
	$out_json['success'] = 0;
}
$out_json['error'] = $error;
echo json_encode($out_json);
?>
