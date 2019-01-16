<?php
require_once 'db_connect.php';
//Function to create a secure session
function secure_session_start(){
    $session_name = 'secure_session_id';
    $secure = TRUE;
    $httpsonly = TRUE;
    if(ini_set('session.use_only_cookies',1)==FALSE){
        header("Location: ../error.php?err=Cannot exclusively use cookies (ini_set)");
        exit();
    }
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httpsonly);
    session_name($session_name);
    session_start();
    session_regenerate_id();
}

//Function to login
function login($email, $password, $mysqli){
    if($stmt = $mysqli->prepare("SELECT email, pass, user_id FROM login WHERE email = ? LIMIT 1")){
        $stmt->bind_param('s',$email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($db_email,$db_password,$db_user_id);
        $stmt->fetch();
        if($stmt->num_rows==1){
            $stmt->close();
            if(password_verify($password,$db_password)){
				if ($stmt = $mysqli->prepare("SELECT fname, lname FROM users WHERE user_id =? LIMIT 1")){
					$stmt->bind_param('i',$db_user_id);
					$stmt->execute();
					$stmt->store_result();
					$stmt->bind_result($db_fname, $db_lname);
					$stmt->fetch();
				
					
					$user_browser=$_SERVER['HTTP_USER_AGENT'];
					$_SESSION['user_id'] = $db_user_id;
					$_SESSION['email']= $db_email;
					$_SESSION['name']=$db_fname;
					$_SESSION['login_string']=hash('sha512',$db_password.$user_browser);
					return true;
				}else {return false;}
			}else {return false;}
		}else {return false;}
	}else {return false;}
}

//Function to check if user is logged in
function login_check($mysqli){
    if(isset($_SESSION['user_id'], $_SESSION['email'], $_SESSION['login_string'], $_SESSION['name'])){
        $user_id = $_SESSION['user_id'];
        $email=$_SESSION['email'];
		$name=$_SESSION['name'];
		$login_string=$_SESSION['login_string'];
        $user_browser= $_SERVER['HTTP_USER_AGENT'];
        if($stmt=$mysqli->prepare("SELECT pass FROM login WHERE email = ? LIMIT 1")){
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($db_password);
            $stmt->fetch();
            if($stmt->num_rows==1){
                $login_check=hash('sha512',$db_password.$user_browser);
                if(hash_equals($login_check,$login_string)){
                    return true;
                }else {return false;}
            }else {return false;}
        }else {return false;}
    }else {return false;}
}
?>