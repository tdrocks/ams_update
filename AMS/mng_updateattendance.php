<?php
	// error_reporting(0);
	session_start();
	require 'database.php';
	if(isset($_SESSION['mng_email'])){
	
	if(isset($_POST['submit'])){

    	date_default_timezone_set('Asia/Kolkata');
        $link = mysqli_connect("localhost", "root", "sprinklr@ams", "AMS_2");
    	$emp_email = $_POST['employee'];
    	$date = $_POST['date'];
    	$shift = $_POST['shift'];
    	$special = $_POST['special'];
        $month = date('m',strtotime($date));
        $current_date = date('d',strtotime($date));
        if($current_date>15){
          $month = $month+1;
        }
        else{
          $month = $month;
        }  

        if($special == "present"){
            $dayallowance = $_POST['dayallowance'];
            $shiftallowance = $_POST['shiftallowance'];
        }

        else if ($special == "public_holiday") {
            $dayallowance = 2000;
            $shiftallowance = $_POST['shiftallowance'];
        }

        else if ($special == "pto" || $special == "up" || $special == "comp_off") {
            $dayallowance = 0;
            $shiftallowance = 0;
        }

        if($special != "public_holiday"){
            $records0 = $conn->prepare('SELECT * FROM attendance WHERE emp_email = :emp_email and att_date = :date0');
            $records0->bindParam(':emp_email', $emp_email);
            $records0->bindParam(':date0', $date);

            if($records0->execute()){

                $results0 = $records0->fetch(PDO::FETCH_ASSOC);
                $olddayallowance = $results0['dayallowance'];
                $oldshiftallowance = $results0['shiftallowance'];
                $newdayallowance = $dayallowance-$results0['dayallowance'];
                $newshiftallowance = $shiftallowance-$results0['shiftallowance'];


                if ($newdayallowance == 0 && $newshiftallowance == 0){

                    $records2 = $conn->prepare('UPDATE attendance SET shift = :shift, remarks = :remarks WHERE emp_email = :emp_email and att_date = :date1');
                    $records2->bindParam(':emp_email', $emp_email);
                    $records2->bindParam(':date1', $date);
                    $records2->bindParam(':shift', $shift);
                    $records2->bindParam(':remarks', $special);
                    if ($records2->execute()) {
                        header("Location: /AMS/userdashboard.php");
                    }
                    else{
                        echo "Updation Failed!";
                    }
                }

                else{
                    if ($newdayallowance < 0) {
                        $newdayallowance = 0;
                    }
                    if ($newshiftallowance < 0) {
                        $newshiftallowance = 0;
                    }
                    $records = $conn->prepare('UPDATE attendance SET dayallowance = :dayallowance, shiftallowance = :shiftallowance, shift = :shift, remarks = :remarks WHERE emp_email = :emp_email and att_date = :date1');
                    $records->bindParam(':emp_email', $emp_email);
                    $records->bindParam(':date1', $date);
                    $records->bindParam(':shift', $shift);
                    $records->bindParam(':dayallowance', $newdayallowance);
                    $records->bindParam(':shiftallowance', $newshiftallowance);
                    $records->bindParam(':remarks', $special);

                    if($records->execute()){
                        $sql = "SELECT * from payroll WHERE emp_email = '$emp_email' and month = '$month'";
                        $result=mysqli_query($link,$sql);
                        $row = mysqli_fetch_array($result);
                        if(mysqli_num_rows($result) > 0){
                            $total = $row["total_allowance"];
                            $total1 = $total - $olddayallowance;
                            $total2 = $total1 - $oldshiftallowance;
                            $new_total = $total2 + $newdayallowance + $newshiftallowance;

                            $records3 = $conn->prepare('SELECT COUNT(*) as total_count FROM attendance WHERE emp_email = :emp_email and month = :month and remarks = "present"');
                            $records3->bindParam(':emp_email', $emp_email);
                            $records3->bindParam(':month', $month);

                            $records3->execute();
                            $results3 = $records3->fetch(PDO::FETCH_ASSOC);
                            $total_count = $results3['total_count'];

                            $records1 = $conn->prepare('UPDATE payroll SET total_allowance = :new_total, working_days = :w_days where emp_email = :emp_email and month = :month');
                            $records1->bindParam(':new_total', $new_total);
                            $records1->bindParam(':emp_email', $emp_email);
                            $records1->bindParam(':month', $month);
                            $records1->bindParam(':w_days', $total_count);
                            $records1->execute();
                        }
                        header("Location: /AMS/userdashboard.php");
                    }
                }
            }
            else{
                echo "No Record Exists!";
            }
        }

        else{
            $records0 = $conn->prepare('SELECT * FROM attendance WHERE emp_email = :emp_email and att_date = :date0');
            $records0->bindParam(':emp_email', $emp_email);
            $records0->bindParam(':date0', $date);

            if($records0->execute()){
                $results0 = $records0->fetch(PDO::FETCH_ASSOC);
                $olddayallowance = $results0['dayallowance'];
                $oldshiftallowance = $results0['shiftallowance'];

                $records = $conn->prepare('UPDATE attendance SET dayallowance = :dayallowance, shiftallowance = :shiftallowance, shift = :shift, remarks = :remarks WHERE emp_email = :emp_email and att_date = :date1');
                $records->bindParam(':emp_email', $emp_email);
                $records->bindParam(':date1', $date);
                $records->bindParam(':shift', $shift);
                $records->bindParam(':dayallowance', $dayallowance);
                $records->bindParam(':shiftallowance', $shiftallowance);
                $records->bindParam(':remarks', $special);

                if($records->execute()){
                    $sql = "SELECT * from payroll WHERE emp_email = '$emp_email' and month = '$month'";
                    $result=mysqli_query($link,$sql);
                    $row = mysqli_fetch_array($result);
                    if(mysqli_num_rows($result) > 0){
                        $total = $row["total_allowance"];
                        $total1 = $total - $olddayallowance;
                        $total2 = $total1 - $oldshiftallowance;
                        $new_total = $total2 + $dayallowance + $shiftallowance;

                        $records3 = $conn->prepare('SELECT COUNT(*) as total_count FROM attendance WHERE emp_email = :emp_email and month = :month and (remarks = "present" OR remarks = "public_holiday")');
                        $records3->bindParam(':emp_email', $emp_email);
                        $records3->bindParam(':month', $month);

                        $records3->execute();
                        $results3 = $records3->fetch(PDO::FETCH_ASSOC);
                        $total_count = $results3['total_count'];

                        $records1 = $conn->prepare('UPDATE payroll SET total_allowance = :new_total, working_days = :w_days where emp_email = :emp_email and month = :month');
                        $records1->bindParam(':new_total', $new_total);
                        $records1->bindParam(':emp_email', $emp_email);
                        $records1->bindParam(':month', $month);
                        $records1->bindParam(':w_days', $total_count);
                        $records1->execute();
                    }
                }
            }
        }
	}
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
	<link href="/AMS/images/favicon.ico" rel="shortcut icon">
	<title>Update Attendance</title>
	<link rel="stylesheet" type="text/css" href="style.css">
    <link href='http://fonts.googleapis.com/css?family=Comfortaa' rel='stylesheet' type='text/css'>
    <script type="text/javascript">   	
    	function dayselected(){
    		var dayallow = 0;
    		// var date = new Date(document.getElementById('date').value);
    		// if(date){
	    	// 	if(date.getDay() == 6 || date.getDay() == 0){
	    	// 		dayallow = 2000;
	    	// 	}
	    		document.getElementById('dayallowance').value = dayallow;    		
    		// }
    	}
    	function shiftselected() {
    		var shiftallow = 0;
    		var shift = document.getElementById('shift').value;
    		if (shift) {
    			if(shift == "5 PM to 2 AM" || shift == "10 PM to 7 AM" || shift == "7 PM to 1 AM" || shift == "1 AM to 7 AM"){
    				shiftallow = 1500;
    			}
    			document.getElementById('shiftallowance').value = shiftallow;
    		}
    	}

    </script>
    <style>
            table {
                border-collapse: collapse;
                width: 100%;
            }
            td, th {
                border: 3px solid #75ff89;
                text-align: centre;
                padding: 8px;
                background-color: #75ff33;
            }
        </style>
</head>
<body>
      <div class="background">
        <center>
        <div class="layer">
            <?php if( !empty($_SESSION["mng_email"]) ): ?>
            <table>
                <tr>
                    <th style="font-size:30px;">Update Attendance</th>
                </tr>
            </table>
                <h3>Manager Name: <?php echo $_SESSION["mng_name"]; ?></h3>
                <h3>Module: <?php echo $_SESSION['mng_module']; ?></h3>
            <form action="mng_updateattendance.php" method="POST">
                        <label>Employee</label>
                        <select name="employee" id="employee" style="border-radius: 5px; border:1px solid #DAF7A6; padding: 2px;" required="">
                            <?php
                                $records = $conn->prepare("SELECT * FROM employee WHERE emp_mng = :mng_email");
                                $records->bindParam(':mng_email',$_SESSION['mng_email']);
                                if($records->execute()){
                                    while ($row = $records->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<option value = ".$row['emp_email'].">".$row['emp_name']."</option>";
                                    }
                                }
                            ?>
                        </select>
                        <label>Date</label>
                      	<input type="date" name="date" id="date" style="border-radius: 5px; border:1px solid #DAF7A6; padding: 2px;" onchange="dayselected()" required="">
                      	<label>Shift</label>
                      	<select name="shift" id="shift" style="border-radius: 5px; border:1px solid #DAF7A6; padding: 2px;" onchange="shiftselected()" required="">
                      		    <option selected disabled hidden style='display: none' value=''>Please Select Shift</option>
                  				<option disabled value=''>Weekday Shifts</option>
                  				<option value="6 AM to 3 PM">6 AM to 3 PM</option>
                  				<option value="9 AM to 6 PM">9 AM to 6 PM</option>
                  				<option value="11 AM to 8 PM">11 AM to 8 PM</option>
                  				<option value="12 PM to 9 PM">12 PM to 9 PM</option>
                  				<option value="2 PM to 11 PM">2 PM to 11 PM</option>
                  				<option value="5 PM to 2 AM">5 PM to 2 AM</option>
                  				<option value="10 PM to 7 AM">10 PM to 7 AM</option>
                  				<option disabled value=''>Weekend Shifts</option>
                  				<option value="7 AM to 1 PM">7 AM to 1 PM</option>
                  				<option value="1 PM to 7 PM">1 PM to 7 PM</option>
                  				<option value="7 PM to 1 AM">7 PM to 1 AM</option>
                  				<option value="1 AM to 7 AM">1 AM to 7 AM</option>
                  			    <option disabled value=''>Others</option>
                                <option value="NA">Not Applicable</option>
                        </select>
                        </br>
                        <input type="radio" name="special" id="special" value="pto"> PTO
                        <input type="radio" name="special" id="special" value="up"> UP
                        <input type="radio" name="special" id="special" value="comp_off"> Comp Off
                        <input type="radio" name="special" id="special" value="public_holiday"> Public Holiday
                        <input type="radio" name="special" id="special" value="present" checked> Present
                        <br>
                  			<h3>Extra Allowances</h3>
                  			<!-- Weekend Allowance:<input type="integer" name="dayallowance" id="dayallowance" style="border-radius: 5px; border:1px solid #DAF7A6; padding: 2px;" readonly="true"> -->
                  			Shift Allowance:<input type="integer" name="shiftallowance" id="shiftallowance" style="border-radius: 5px; border:1px solid #DAF7A6; padding: 2px;" readonly="true">
                        </br>
                        </br>
                  			<input type="submit" name="submit">
            </form>
            <button onclick="window.location='/AMS/managerdashboard.php';">Go Back</button>
            <?php else: ?>
                <?php header("Location: /AMS"); ?>
            <?php endif; ?>
        </div>
        </center>
      </div>
</body>
</html>