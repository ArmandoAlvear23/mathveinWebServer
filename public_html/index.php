<?php
require_once __DIR__.'/../required/db_connect.php';
require_once __DIR__.'/../required/functions.php';
secure_session_start();
if(login_check($mysqli)){
    header("Location: protected_page.php");
}
?>

<html>
    <head>
        <meta charset="utf-8">
        <title>MATHVEIN Login</title>
    </head>
    <body>
        <div name="title">Login</div>
        <form action ="scripts/process_login.php" method="post" id="loginForm">
            <div id="inputName">Email:</div>
            <input type="text" name="email" id="email" />
            <div id="inputName">Password:</div>
            <input type="password" name="password" id="password"/><br><br>
            <input type="submit" value="Login"/>
        </form>
        <?php
        if(isset($_GET["error"])){
            echo '<div class="error">Error Signing In!</div>';
        }
        ?>
    </body>
</html>