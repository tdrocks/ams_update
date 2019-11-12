<?php  
//export.php  
error_reporting(0);
session_start();
require 'database.php';

if(isset($_POST["export_payroll"]) && isset($_SESSION['mng_email']))
{
    $records = $conn->prepare("SELECT * FROM employee");
    $records->execute();

    $output .= '<table id="attendance" bordered="1">  
                        <tr>  
                            <th>Emp Id</th>
                            <th>Emp Name</th>
                            <th>Month</th>
                            <th>Working Days</th>
                            <th>Night Shifts</th>
                            <th>Public Holidays</th>
                            <th>PTOs</th>
                            <th>Total Allowance</th>  
                        </tr>';

    while($row = $records->fetch(PDO::FETCH_ASSOC)){

        $records1 = $conn->prepare("SELECT * FROM payroll WHERE emp_email = :emp_email and month = :month");
        $records1->bindParam(':emp_email',$row["emp_email"]);
        $records1->bindParam(':month',$_POST['month']);
        $records1->execute();

        $records2 = $conn->prepare('SELECT COUNT(*) as night_shifts FROM attendance WHERE emp_email = :emp_email AND month = :month AND (shift="5 PM to 2 AM" OR shift="10 PM to 7 AM") AND (remarks="public_holiday" OR remarks="present")');
        $records2->bindParam(':emp_email',$row["emp_email"]);
        $records2->bindParam(':month',$_POST['month']);
        $records2->execute();
        $row2 = $records2->fetch(PDO::FETCH_ASSOC);
        $ns = $row2["night_shifts"];

        $records3 = $conn->prepare('SELECT COUNT(*) as public_holidays FROM attendance WHERE emp_email = :emp_email AND month = :month AND (remarks="public_holiday")');
        $records3->bindParam(':emp_email',$row["emp_email"]);
        $records3->bindParam(':month',$_POST['month']);
        $records3->execute();
        $row3 = $records3->fetch(PDO::FETCH_ASSOC);
        $ph = $row3["public_holidays"];

        $records4 = $conn->prepare('SELECT COUNT(*) as PTO FROM attendance WHERE emp_email = :emp_email AND month = :month AND (remarks="pto")');
        $records4->bindParam(':emp_email',$row["emp_email"]);
        $records4->bindParam(':month',$_POST['month']);
        $records4->execute();
        $row4 = $records4->fetch(PDO::FETCH_ASSOC);
        $pto = $row4["PTO"];

        if($records1->execute())
        {
        
            while($row1 = $records1->fetch(PDO::FETCH_ASSOC))
            {
            $output .= '
            <tr>
                <td>'.$row["emp_id"].'</td>
                <td>'.$row["emp_name"].'</td>
                <td>'.$row1["month"].'</td>
                <td>'.$row1["working_days"].'</td>
                <td>'.$row2["night_shifts"].'</td>
                <td>'.$row3["public_holidays"].'</td>
                <td>'.$row4["PTO"].'</td>
                <td>'.$row1["total_allowance"].'</td>   
            </tr>
            ';
            }
        }
    }
    $output .= '</table>';
    header('Content-Type: application/xls');
    header('Content-Disposition: attachment; filename=Payroll_'.$_POST['month'].'_'.$_SESSION['mng_email'].'.xls');
    echo $output;
}
?>