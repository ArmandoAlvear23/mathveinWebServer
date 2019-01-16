<?php
require_once __DIR__.'/../required/db_connect.php';
require_once __DIR__.'/../required/functions.php';
secure_session_start();
?>

<!DOCTYPE htm>
<html>
    <head
    <meta charset="UTF-8">
        <title> MATHVEIN-Sleep History</title>
        </head>
        <body>
            <center>
            =========================================================</br>
			<div>
            <?php if(login_check($mysqli)): ?>
			<?php
            if($stmt=$mysqli->prepare("SELECT start_time, end_time, start_time_am_pm, end_time_am_pm, timestamp FROM sleep WHERE user_id=? ORDER BY timestamp DESC LIMIT 20")){
				$user_id = 1;
				$stmt->bind_param('i', $user_id);
                $stmt->execute();
                $stmt->bind_result($db_start_time, $db_end_time, $db_start_time_am_pm, $db_end_time_am_pm, $db_timestamp);
				echo("SLEEP HISTORY</br>");
				echo "<table border=\"1\" align=\"center\">";
				echo "<tr><th>Start Time</th>";
				echo "<th>Am(0)/PM(1)</th>";
				echo "<th>End Time</th>";
				echo "<th>Am(0)/PM(1)</th>";
				echo "<th>Timestamp</th></tr>";
                while($stmt->fetch()){
					echo "<tr><td>";
                    echo $db_start_time;
					echo "</td><td>";
					echo $db_start_time_am_pm;
					echo "</td><td>";
					echo $db_end_time;
					echo "</td><td>";
					echo $db_end_time_am_pm;
					echo "</td><td>";
					echo $db_timestamp;
					echo "</td></tr>";
                }
                $stmt->close();
                echo "</table>";
            }
            else{ echo "error";}
            $mysqli->close();
            ?>
			</div>
            
            <ul>
              <li><a href="protected_page.php">Back</a></li>
              <li><a href="scripts/process_logout.php">Logout</a></li>
            </ul>
            
            
            <?php else: ?>
            <p>
                <span class='error'>Not authorized to access this page.</span>Please<ahref="index.php"> login</a>
            </p>
            <?php endif; ?>
            =========================================================</br>
            </center>
        </body>
        
</html>