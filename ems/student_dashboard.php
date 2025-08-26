<?php session_start(); if($_SESSION["role"]!="student"){header("Location: login.php");exit;} ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h2>Student Dashboard</h2>
        <div class="list-group">
            <a href="showcourses.php" class="list-group-item list-group-item-action">Show Available Courses</a>
            <a href="advisingadd.php" class="list-group-item list-group-item-action">Add Advising</a>
            <a href="studentgrade.php" class="list-group-item list-group-item-action">Check My Grades</a>
        </div>
        
        <div class="mt-3">
            <a href="login.php" class="btn btn-secondary">Logout</a>
        </div>
    </div>
</body>
</html>