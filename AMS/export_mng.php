<?php  
//export.php  
error_reporting(0);
session_start();
require 'database.php';

if(isset($_POST["export_mng"]) && isset($_SESSION['mng_email']))
{
    $records = $conn->prepare("SELECT * FROM employee WHERE emp_mng = :mng_email");
    $records->bindParam(':mng_email',$_SESSION['mng_email']);
    $records->execute();

    $output .= '<table id="attendance" bordered="1">  
                        <tr>  
                            <th>Emp Email</th>
                            <th>Emp Name</th>
                            <th>Date</th>  
                            <th>Shift</th>  
                            <th>Day Allowance</th>
                            <th>Shift Allowance</th>
                            <th>Remarks</th>
                        </tr>';

    while($row = $records->fetch(PDO::FETCH_ASSOC)){

        $records1 = $conn->prepare("SELECT * FROM attendance WHERE emp_email = :emp_email and month = :month");
        $records1->bindParam(':emp_email',$row["emp_email"]);
        $records1->bindParam(':month',$_POST["month"]);
        $records1->execute();

        if($records1->execute())
        {
            while($row1 = $records1->fetch(PDO::FETCH_ASSOC))
            {
            $output .= '
            <tr>
                <td>'.$row["emp_email"].'</td>
                <td>'.$row["emp_name"].'</td>
                <td>'.$row1["att_date"].'</td>  
                <td>'.$row1["shift"].'</td>  
                <td>'.$row1["dayallowance"].'</td>  
                <td>'.$row1["shiftallowance"].'</td>  
                <td>'.$row1["remarks"].'</td>
            </tr>
            ';
            }

        }
    }
    $output .= '</table>';
    header('Content-Type: application/xls');
    header('Content-Disposition: attachment; filename=Attendance_'.$_POST['month'].'_'.$_SESSION['mng_email'].'.xls');
    echo $output;
}
?>