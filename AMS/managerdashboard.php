<?php
    session_start();
    require 'database.php';

    if( isset($_SESSION['mng_email']) ){

    $records = $conn->prepare('SELECT * FROM managers WHERE mng_email = :mng_email');
    $records->bindParam(':mng_email', $_SESSION['mng_email']);
    $records->execute();
    $results = $records->fetch(PDO::FETCH_ASSOC);

    $admin = NULL;

    if( count($results) > 0){
        $admin = $results;
    }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="style.css">
        <link href='http://fonts.googleapis.com/css?family=Comfortaa' rel='stylesheet' type='text/css'>
        <link href="/AMS/images/favicon.ico" rel="shortcut icon">
        <title>Manager Dashboard</title>
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
    <body><div class="background">
            <center>
            <div class="layer">
                <?php if( !empty($admin) ): ?>
                <table>
                    <tr>
                        <th style="font-size:50px;">Manager Dashboard</th>
                    </tr>
                </table>
                    <h2>Welcome <?= $admin['mng_name']; ?></h2>
                    <h2>Module: <?= $admin['mng_module'] ?></h2>
                    <input type="submit" value="Update Attendance" onclick="window.location='/AMS/mng_updateattendance.php';" />
                    <input type="submit" value="View Attendance" onclick="window.location='/AMS/mng_prevattendance.php';" />
                    </br>
                    <a href="logout.php" style="margin-top: 50px;font-size: 20px;">Logout</a>
                <?php else: ?>
                    <?php header("Location: /AMS/managerlogin.php"); ?>
                <?php endif; ?>
            </div>
            </center>
        </div>
    </body>
</html>