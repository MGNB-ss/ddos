<?php 
include 'dbc.php';

$err = array();

foreach($_GET as $key => $value) {
	$get[$key] = filter($value); 
}

if ($_POST['doLogin']=='Login')
{

foreach($_POST as $key => $value) {
	$data[$key] = filter($value); 
}


$user_email = $data['usr_email'];
$pass = $data['pwd'];


if (strpos($user_email,'@') === false) {
    $user_cond = "user_name='$user_email'";
} else {
      $user_cond = "user_email='$user_email'";
    
}

	
$result = mysql_query("SELECT `id`,`pwd`,`full_name`,`approved`,`user_level` FROM users WHERE $user_cond AND `banned` = '0'") or die (mysql_error()); 
$num = mysql_num_rows($result);

    if ( $num > 0 ) { 
	
	list($id,$pwd,$full_name,$approved,$user_level) = mysql_fetch_row($result);
	
	if(!$approved) {
	$err[] = "Account not activated. This community requires approval by an administrator. If you have purchased access to this website simply contact the vendor with your login details and payment information.";
	 }

	if ($pwd === PwdHash($pass,substr($pwd,0,9))) { 
	if(empty($err)){			
  
       session_start();
	   session_regenerate_id (true); //prevent against session fixation attacks.

		$_SESSION['user_id']= $id;  
		$_SESSION['user_name'] = $full_name;
		$_SESSION['user_level'] = $user_level;
		$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
		$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
		
		$stamp = time();
		$ckey = GenKey();
		mysql_query("update users set `ctime`='$stamp', `ckey` = '$ckey' where id='$id'") or die(mysql_error());
		
		
                if(isset($_POST['remember']))
			{
				  setcookie("user_id", $_SESSION['user_id'], time()+60*60*24*COOKIE_TIME_OUT, "/");
				  setcookie("user_key", sha1($ckey), time()+60*60*24*COOKIE_TIME_OUT, "/");
				  setcookie("user_name",$_SESSION['user_name'], time()+60*60*24*COOKIE_TIME_OUT, "/");
			}
		  header("Location: index.php");
		 }
       } else {
            $err[] = "You have supplied an invalid password for this username.";
        }
                    } else {
		$err[] = "The username provided does not exist in our database.";
	  }		
}
					 
					 

?>
<html>
<head>
<title>Login Required</title>
<center>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/javascript" src="javascript/jquery-1.3.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="javascript/jquery.validate.js"></script>
  <script>
  $(document).ready(function(){
    $("#logForm").validate();
  });
  </script>
<link href="images/styles.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="content"> 
<p><?php

	if(!empty($err))  {
	echo "<div class=\"msg\">";
	foreach ($err as $e) {
	    echo "$e <br>";
	}
	echo "</div>";	
	}  
	   
?></p>


<br>
<div class="box"><h2>Login</h2>
<div class="box-content">
   

<center>Login to your account below.</center>
<form action="login.php" method="post" name="logForm" id="logForm" >
Username: <input name="usr_email" type="text" class="required" id="txtbox" size="25"><br>
Password: <input name="pwd" type="password" class="required password" id="txtbox" size="25"><br><br>
<input name="doLogin" type="submit" id="doLogin3" value="Login">
<input name="remember" type="checkbox" id="remember" value="1"> Remember Me</div><br>


<br />

Not registered? <a href="register.php">Click here</a> to register an account.
       
        <div align="center"></div>
        <p align="center">&nbsp; </p>
      </form>
</div>
</div>

</body>
</html>
