<?php
require_once __DIR__.'/../required/db_connect.php';
require_once __DIR__.'/../required/functions.php';
secure_session_start();
?>

<!DOCTYPE htm>
<html>
    <head
    <meta charset="UTF-8">
        <title> MATHVEIN-Meal History</title>
        </head>
        <body>
            <center>
            ========================================================================</br>
			<div>
            <?php if(login_check($mysqli)): ?>
			<?php
            if($stmt=$mysqli->prepare("SELECT appetizer, main, dessert, snack, drink, timestamp FROM meals WHERE user_id=? ORDER BY timestamp DESC LIMIT 20")){
				$user_id = 1;
				$stmt->bind_param('i', $user_id);
                $stmt->execute();
                $stmt->bind_result($db_appetizer, $db_main, $db_dessert, $db_snack, $db_drink, $db_timestamp);
				echo("MEAL HISTORY</br>");
				echo "<table border=\"1\" align=\"center\">";
				echo "<tr><th>Appetizer</th>";
				echo "<th>Main Dish</th>";
				echo "<th>Dessert</th>";
				echo "<th>Snack</th>";
				echo "<th>Drink</th>";
				echo "<th>Timestamp</th></tr>";
                while($stmt->fetch()){
					echo "<tr><td>";
                    echo $db_appetizer;
					echo "</td><td>";
					echo $db_main;
					echo "</td><td>";
					echo $db_dessert;
					echo "</td><td>";
					echo $db_snack;
					echo "</td><td>";
					echo $db_drink;
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
            ========================================================================</br>
            </center>
        </body>
</html>