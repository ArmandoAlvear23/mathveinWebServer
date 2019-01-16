<?php
require_once __DIR__.'/../required/db_connect.php';
require_once __DIR__.'/../required/functions.php';
secure_session_start();
?>

<!DOCTYPE htm>
<html>
    <head
    <meta charset="UTF-8">
        <title> MATHVEIN-Main Page</title>
        </head>
        <body>
            <center>
            Welcome!</br>
            =============================================</br>
			<div>
            <?php if(login_check($mysqli)): ?>
            <?php
            if($stmt=$mysqli->prepare("SELECT systolic, diastolic, blood_glucose, last_update FROM current_vitals WHERE user_id=? LIMIT 1")){
				$user_id = 1;
				$stmt->bind_param('i', $user_id);
                $stmt->execute();
                $stmt->bind_result($db_systolic, $db_diastolic, $db_blood_glucose, $db_timestamp);
				echo("CURRENT VITALS</br>");
				echo "<table border=\"1\" align=\"center\">";
				echo "<tr><th>Diastolic</th>";
				echo "<th>Systolic</th>";
				echo "<th>Blood Glucose</th>";
				echo "<th>Last Update</th></tr>";
                while($stmt->fetch()){
					echo "<tr><td>";
                    echo $db_systolic;
					echo "</td><td>";
					echo $db_diastolic;
					echo "</td><td>";
					echo $db_blood_glucose;
					echo "</td><td>";
					echo $db_timestamp;
					echo "</td><tr>";
                }
                $stmt->close();
                echo "</table>";
            }
            else{ echo "error";}
            ?>
			<?php
            if($stmt=$mysqli->prepare("SELECT systolic, diastolic, blood_glucose, timestamp FROM vitals_history WHERE user_id=? ORDER BY timestamp DESC")){
				$user_id = 1;
				$stmt->bind_param('i', $user_id);
                $stmt->execute();
                $stmt->bind_result($db_systolic, $db_diastolic, $db_blood_glucose, $db_timestamp);
				
				echo("VITALS HISTORY</br>");
				echo "<table border=\"1\" align=\"center\">";
				echo "<tr><th>Diastolic</th>";
				echo "<th>Systolic</th>";
				echo "<th>Blood Glucose</th>";
				echo "<th>Timestamp</th></tr>";
                while($stmt->fetch()){
					echo "<tr><td>";
                    echo $db_systolic;
					echo "</td><td>";
					echo $db_diastolic;
					echo "</td><td>";
					echo $db_blood_glucose;
					echo "</td><td>";
					echo $db_timestamp;
					echo "</td><tr>";
                }
                $stmt->close();
                echo "</table>";
            }
            else{ echo "error";}
			$mysqli->close();
            ?>
            
            <ul>
              <li><a href="meal_page.php">Meal History</a></li>
              <li><a href="exercise_page.php">Exercise History</a></li>
              <li><a href="work_page.php">Work History</a></li>
              <li><a href="sleep_page.php">Sleep History</a></li>
              <li><a href="scripts/process_logout.php">Logout</a></li>
            </ul>
            
            <?php else: ?>
            <p>
                <span class='error'>Not authorized to access this page.</span>Please<ahref="index.php"> login</a>
            </p>
            <?php endif; ?>
            </br>
            </center>
        </body>
</html>