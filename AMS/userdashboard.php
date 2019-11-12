<?php
    session_start();
    require 'database.php';

    if( isset($_SESSION["emp_email"]) ){

        $records = $conn->prepare('SELECT * FROM employee WHERE emp_email = :email');
        $records->bindParam(':email', $_SESSION["emp_email"]);
        $records->execute();
        $results = $records->fetch(PDO::FETCH_ASSOC);

        $records1 = $conn->prepare('SELECT * FROM managers WHERE mng_email = :mng_email');
        $records1->bindParam(':mng_email', $_SESSION["emp_manager"]);
        $records1->execute();
        $results1 = $records1->fetch(PDO::FETCH_ASSOC);

    $manager = NULL;
    $user = NULL;

    if( count($results) > 0){
        $user = $results;
        $manager = $results1;
        $_SESSION["manager_name"] = $manager["mng_name"];
    }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css">
        <link href='http://fonts.googleapis.com/css?family=Comfortaa' rel='stylesheet' type='text/css'>
        <link href="/AMS/images/favicon.ico" rel="shortcut icon">
        <title>Employee Dashboard</title>
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
                <?php if( !empty($user) ): ?>
                <table>
                    <tr>
                        <th style="font-size:50px;">Employee Dashboard</th>
                    </tr>
                </table>
                    <h2>Welcome <?php echo $user['emp_name']; ?></h2>
                    <h2>Module: <?php echo $user['emp_module']; ?>,   Manager: <?php echo $manager['mng_name']; ?></h2>
                    <input type="submit" value="Upload Attendance" onclick="window.location='/AMS/multi_attendance.php';" />
                    <input type="submit" value="Previous Attendance" onclick="window.location='/AMS/prevattendance.php';" />
                    </br>
                    <a href="logout.php" style="margin-top: 50px;font-size: 20px;">Logout</a>
                <?php else: ?>
                    <?php header("Location: /AMS"); ?>
                <?php endif; ?>
            </div>
            </center>
        </div>
    </body>
</html>
