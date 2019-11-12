<?php  
//export.php  
error_reporting(0);
session_start();
require 'database.php';

if(isset($_POST["export"]) && isset($_SESSION['emp_email']))
{
  $emp_email = $_SESSION['emp_email'];
 $records = $conn->prepare("SELECT * FROM attendance WHERE emp_email = :emp_email and month = :month");
 $records->bindParam(':emp_email',$_SESSION['emp_email']);
 $records->bindParam(':month',$_POST["month"]);
 if($records->execute())
 {
  $output .= '<table id="attendance" bordered="1">  
                    <tr>  
                         <th>Date</th>  
                         <th>Shift</th>  
                         <th>Day Allowance</th>
                         <th>Shift Allowance</th>
                         <th>Remarks</th>
                    </tr>';
  while($row = $records->fetch(PDO::FETCH_ASSOC))
  {
   $output .= '
    <tr>  
      <td>'.$row["att_date"].'</td>  
      <td>'.$row["shift"].'</td>  
      <td>'.$row["dayallowance"].'</td>  
      <td>'.$row["shiftallowance"].'</td>
      <td>'.$row["remarks"].'</td>  
    </tr>
   ';
  }
  $output .= '</table>';
  header('Content-Type: application/xls');
  header('Content-Disposition: attachment; filename='.$emp_email.'_'.$_POST["month"].'attendance.xls');
  echo $output;
 }
}
?>