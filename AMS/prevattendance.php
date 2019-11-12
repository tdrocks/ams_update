<?php
	session_start();
	require 'database.php';

	if(isset($_SESSION['emp_email'])){
		echo "<link href=\"/AMS/images/favicon.ico\" rel=\"shortcut icon\">";
		echo "<center><h2 style=\"margin-top:50px;\">Previous Attendance History</h2></center>";
		echo "</br>
		<form action=\"export.php\" method=\"post\">
			</br>
			<select name=\"month\" id=\"month\" style=\"border-radius: 5px; border:1px solid #DAF7A6; padding: 2px;\" required=\"\">
				<option selected disabled hidden style='display: none' value=''>Select</option>
				<option value=\"01\">January</option>
				<option value=\"02\">February</option>
				<option value=\"03\">March</option>
				<option value=\"04\">April</option>
				<option value=\"05\">May</option>
				<option value=\"06\">June</option>
				<option value=\"07\">July</option>
				<option value=\"08\">August</option>
				<option value=\"09\">September</option>
				<option value=\"10\">October</option>
				<option value=\"11\">November</option>
				<option value=\"12\">December</option>
			</select>
			<input type=\"submit\" name=\"export\" style=\"border-radius: 5px; border:1px solid #DAF7A6; padding: 2px;\" value=\"Export\" />
		</form>
		<button onclick=\"goBack()\">Go Back</button>";

		$records = $conn->prepare("SELECT * FROM attendance WHERE emp_email = :emp_email");
		$records->bindParam(':emp_email',$_SESSION['emp_email']);
		if($records->execute()){
			echo "<center><table id=\"attendance1\" style=\"width:100%;overflow-wrap: auto;\"><tr><th>Date</th><th>Shift</th><th>Day Allowance</th><th>Shift Allowance</th><th>Option</th></tr>";

			while($row = $records->fetch(PDO::FETCH_ASSOC)){
				$timestamp = strtotime($row["att_date"]);
				$date = date('d-m-Y', $timestamp);

				echo "<tr><td>".$date."</td><td>".$row["shift"]."</td><td>".$row["dayallowance"]."</td><td>".$row["shiftallowance"]."</td><td><a href=\"/AMS/emp_updateattendance.php?date=$date\"/>Update</td></tr>";
			}
			echo "</table></center>";
		}
	}
	else {
		header("Location: /AMS");
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<style type="text/css">
		td, th {
			border: solid 1px #DAF7A6;
		} 
		</style>
		<title>Previous Attendance History</title>
		<link href='/AMS/images/favicon.ico' rel='shortcut icon'>
		<link rel="stylesheet" type="text/css" href="style.css">
	    <link href='http://fonts.googleapis.com/css?family=Comfortaa' rel='stylesheet' type='text/css'>
	    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />  
  		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

	    <script type="text/javascript">
	    	function goBack(){
	    		window.location.href = "/AMS/userdashboard.php";
	    	}
	    </script>
	</head>
	<body>
		
	</body>
</html>