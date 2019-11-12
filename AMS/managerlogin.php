<?php
	session_start();

	if( isset($_SESSION['mng_email']) ){
		header("Location: /AMS/managerdashboard.php");
	}

	require 'database.php';
	if(isset($_POST['mng_submit'])){
	if(!empty($_POST['mng_email']) && !empty($_POST['password2'])):
	$records = $conn->prepare('SELECT * FROM managers WHERE mng_email = :mng_email');
	$records->bindParam(':mng_email', $_POST['mng_email']);
	$records->execute();
	$results = $records->fetch(PDO::FETCH_ASSOC);
	$message = '';

	if(count($results) > 0 && ($_POST['password2']==$results['mng_pass'])){
		$_SESSION['mng_email'] = $results['mng_email'];
		$_SESSION['mng_name'] = $results['mng_name'];
		$_SESSION['mng_module'] = $results['mng_module'];
		header("Location: /AMS/managerdashboard.php");
		echo $message;
	} else {
		$message = 'Sorry, those credentials do not match';
		echo $message;
	}
	endif;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<link href="/AMS/images/favicon.ico" rel="shortcut icon">
	<title>Manager Login</title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<link href="https://fonts.googleapis.com/css?family=Merriweather:300i&display=swap" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Comfortaa' rel='stylesheet' type='text/css'>
</head>
<body>
	<div class="background">
		<center>
		<div class="layer">
	        <h2 style="font-size: 30px;">Manager Login</br></h2>
			<div>
				<center>
					<div style="margin-top: 50px;height: 200px;">
						<form action="managerlogin.php" method="POST">	
							<input type="text" placeholder="Enter your email" name="mng_email" required="">
							<input type="password" placeholder="Enter your password" name="password2" required="">
							<input type="submit" name="mng_submit">
						</form>
					</div>
				</center>
			</div>
			<h2><a href="index.php">Employee Login</a></h2>
		</div>
		</center>
	</div>
</body>
</html>