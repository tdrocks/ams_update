<?php
	error_reporting(0);
	session_start();
	require 'database.php';

    if( isset($_SESSION['emp_email']) ){
        header("Location: /AMS/userdashboard.php");
    }

	if(isset($_POST['app_submit'])){
	if(!empty($_POST['emp']) && !empty($_POST['password1'])):
	
		$records = $conn->prepare('SELECT * FROM employee WHERE emp_email = :emp');
		$records->bindParam(':emp', $_POST['emp']);
		$records->execute();
		$results = $records->fetch(PDO::FETCH_ASSOC);

		$message = '';

		if(count($results) > 0 && ($_POST['password1']==$results['emp_pass'])){
			$_SESSION["emp_id"] = $results["emp_id"];
			$_SESSION["emp_email"] = $results["emp_email"];
			$_SESSION["emp_name"] = $results["emp_name"];
			$_SESSION["emp_module"] = $results["emp_module"];
			$_SESSION["emp_manager"] = $results["emp_mng"];
	        header("Location: /AMS/userdashboard.php");
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
	<title>Sprinklr Attendance Management</title>
	<link rel="stylesheet" type="text/css" href="style.css" />
	<link href="https://fonts.googleapis.com/css?family=Merriweather:300i&display=swap" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Comfortaa' rel='stylesheet' type='text/css'>
</head>
<body>
	<div class="background">
		<center>
		<div class="layer">
	        <h2 style="font-size: 30px;">Employee Login</br></h2>
			<div>
				<center>
					<div style="margin-top: 50px;height: 200px;">
						<form action="index.php" method="POST">	
							<input type="text" placeholder="Enter your email" name="emp" required="">
							<input type="password" placeholder="Enter your password" name="password1" required="">
							<input type="submit" name="app_submit">
						</form>
					</div>
				</center>
			</div>
			<h2><a href="managerlogin.php">Manager Login</a></h2>
		</div>
		</center>
	</div>
</body>
</html>
