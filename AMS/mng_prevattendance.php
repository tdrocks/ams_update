<?php
	session_start();
	require 'database.php';


	echo "<link href=\"/AMS/images/favicon.ico\" rel=\"shortcut icon\">";
	echo "<center><h2 style=\"margin-top:50px;\">Previous Attendance History</h2></center>";
	echo "</br>
		<form action=\"export_mng.php\" method=\"post\" style=\"display: inline;\">
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
			<input type=\"submit\" name=\"export_mng\" style=\"border-radius: 5px; border:1px solid #DAF7A6; padding: 2px;\"value=\"Export\" />
		</form>
		<form action=\"export_payroll.php\" method=\"post\" style=\"display: inline;\">
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
			<input type=\"submit\" name=\"export_payroll\" style=\"border-radius: 5px; border:1px solid #DAF7A6; padding: 2px;\" value=\"Export Payroll\" />
		</form>
		<center><button onclick=\"goBack()\" style=\"display: block;\">Go Back</button></center>";
	if(isset($_SESSION['mng_email'])){
		$records = $conn->prepare("SELECT * FROM employee WHERE emp_mng = :mng_email");
		$records->bindParam(':mng_email',$_SESSION['mng_email']);
		$records->execute();

		while($row = $records->fetch(PDO::FETCH_ASSOC)){

			$records1 = $conn->prepare("SELECT * FROM attendance WHERE emp_email = :emp_email");
			$records1->bindParam(':emp_email',$row["emp_email"]);
			$records1->execute();

			echo "<h3 style=\"margin-top:20px; float:left;\">Employee Name: ".$row["emp_name"]."</h3>";

			echo "<center><div class=\"table-wrapper-scroll-y my-custom-scrollbar\"><table id=\"attendance\" class=\"table table-bordered table-striped mb-0\"><tr><th>Emp email</th><th>Date</th><th>Shift</th><th>Day Allowance</th><th>Shift Allowance</th></tr>";

			while($row1 = $records1->fetch(PDO::FETCH_ASSOC)){
				$timestamp = strtotime($row1["att_date"]);
				$date = date('d-m-Y', $timestamp);

				echo "<tr><td>".$row["emp_email"]."</td><td>".$date."</td><td>".$row1["shift"]."</td><td>".$row1["dayallowance"]."</td><td>".$row1["shiftallowance"]."</td></tr>";
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
	    		window.location.href = "/AMS/managerdashboard.php";
	    	}
	    </script>
	</head>
	<body>
		
	</body>
</html>