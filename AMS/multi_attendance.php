<?php
	// error_reporting(0);
	session_start();
	require 'database.php';
	if(isset($_SESSION['emp_email'])){
	
    	if(isset($_POST['submit'])){

        	date_default_timezone_set('Asia/Kolkata');
        	$emp_email = $_SESSION['emp_email'];
        	
        	$start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
        	$shift = $_POST['shift'];
            $dayallowance = 0;
        	$shiftallowance = $_POST['shiftallowance'];

            while (strtotime($start_date) <= strtotime($end_date)) {
                $weekDay = date('w', strtotime($start_date));
                $month = date('m',strtotime($start_date));  
                $date = date('d',strtotime($start_date));
                if($date>15){
                  $month = $month+1;
                }
                else{
                  $month = $month;
                }                
                if($weekDay == 0 || $weekDay == 6){
                    
                    $records = $conn->prepare('INSERT INTO attendance(emp_email, att_date, shift, dayallowance, shiftallowance, month) values (:emp_email,:date1,:shift,0,0,:month)');
                    $records->bindParam(':emp_email', $emp_email);
                    $records->bindParam(':date1', $start_date);
                    $records->bindParam(':shift', $shift);
                    $records->bindParam(':month', $month);
                    $records->execute();
                }

                else{
                    
                    $records0 = $conn->prepare('INSERT INTO attendance(emp_email, att_date, shift, dayallowance, shiftallowance, month, remarks) values (:emp_email,:date1,:shift,:dayallowance,:shiftallowance,:month,"present")');
                    $records0->bindParam(':emp_email', $emp_email);
                    $records0->bindParam(':date1', $start_date);
                    $records0->bindParam(':shift', $shift);
                    $records0->bindParam(':dayallowance', $dayallowance);
                    $records0->bindParam(':shiftallowance', $shiftallowance);
                    $records0->bindParam(':month', $month);

                    if($records0->execute()){

                        $link = mysqli_connect("localhost", "root", "sprinklr@ams", "AMS_2");

                        $sql = "SELECT * from payroll WHERE emp_email = '$emp_email' and month = '$month'";
                        $result0 = mysqli_query($link,$sql);
                        $row = mysqli_fetch_array($result0);

                        if(mysqli_num_rows($result0) == 0){

                          $records3 = $conn->prepare('SELECT COUNT(*) as total_count FROM attendance WHERE emp_email = :emp_email and month = :month and (remarks = "present" OR remarks = "public_holiday")');
                          $records3->bindParam(':emp_email', $emp_email);
                          $records3->bindParam(':month', $month);

                          $records3->execute();
                          $results3 = $records3->fetch(PDO::FETCH_ASSOC);
                          $total_count = $results3['total_count'];

                          $new_total = $dayallowance + $shiftallowance;
                          $records2 = $conn->prepare('INSERT into payroll values (:emp_email, :total, :month, :w_days)');
                          $records2->bindParam(':emp_email', $emp_email);
                          $records2->bindParam(':total', $new_total);
                          $records2->bindParam(':month', $month);
                          $records2->bindParam(':w_days', $total_count);
                          $records2->execute(); 
                          
                        }
                        else{

                          $records3 = $conn->prepare('SELECT COUNT(*) as total_count FROM attendance WHERE emp_email = :emp_email and month = :month and (remarks = "present" OR remarks = "public_holiday")');
                        $records3->bindParam(':emp_email', $emp_email);
                        $records3->bindParam(':month', $month);

                        $records3->execute();
                        $results3 = $records3->fetch(PDO::FETCH_ASSOC);
                        $total_count = $results3['total_count'];

                          $total = $row['total_allowance'];
                          
                          $new_total1 = $total + $dayallowance + $shiftallowance;
                          $records1 = $conn->prepare('UPDATE payroll SET total_allowance = :new_total1, working_days = :w_days where emp_email = :emp_email and month = :month');
                          $records1->bindParam(':new_total1', $new_total1);
                          $records1->bindParam(':emp_email', $emp_email);
                          $records1->bindParam(':month', $month);
                          $records1->bindParam(':w_days', $total_count);
                          $records1->execute();
                        }
                    }

                }
                $start_date = date ("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
            header("refresh:2;url=index.php"); 
            echo 'Success!';
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
    	// function dayselected(){
    	// 	var dayallow = 0;
    	// 	var date = new Date(document.getElementById('date').value);
    	// 	if(date){
	    // 		if(date.getDay() == 6 || date.getDay() == 0){
	    // 			dayallow = 2000;
	    // 		}
	    // 		document.getElementById('dayallowance').value = dayallow;    		
    	// 	}
    	// }
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
            <?php if( !empty($_SESSION["emp_email"]) ): ?>
            <table>
                <tr>
                    <th style="font-size:30px;">Update Attendance</th>
                </tr>
            </table>
            <h3>Employee Name: <?php echo $_SESSION["emp_name"]; ?></h3>
            <h3>Module: <?php echo $_SESSION['emp_module']; ?>,   Manager: <?php echo $_SESSION['manager_name']; ?></h3>
            <form action="multi_attendance.php" method="POST">
                <label>Start Date</label>
              	<input type="date" name="start_date" id="start_date" style="border-radius: 5px; border:1px solid #DAF7A6; padding: 2px;" required="">
                <label>End Date</label>
                <input type="date" name="end_date" id="end_date" style="border-radius: 5px; border:1px solid #DAF7A6; padding: 2px;" required="">
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
          			</select>
          			<br>
          			<h3>Extra Allowances</h3>
          			<!-- Weekend Allowance:<input type="integer" name="dayallowance" id="dayallowance" style="border-radius: 5px; border:1px solid #DAF7A6; padding: 2px;" readonly="true">
                </br> -->
          			Shift Allowance:<input type="integer" name="shiftallowance" id="shiftallowance" style="border-radius: 5px; border:1px solid #DAF7A6; padding: 2px;" readonly="true">
                </br>
                </br>
          		<input type="submit" name="submit">
            </form>
            <button onclick="window.location='/AMS/userdashboard.php';">Go Back</button>
            <?php else: ?>
                <?php header("Location: /AMS"); ?>
            <?php endif; ?>
        </div>
        </center>
      </div>
</body>
</html>