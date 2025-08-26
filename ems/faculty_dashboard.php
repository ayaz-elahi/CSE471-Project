<?php 
session_start(); 
if($_SESSION["role"]!="faculty"){
    header("Location: login.php");
    exit;
} 
$faculty_id = $_SESSION['id']; // Get the logged-in faculty's ID
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h2>Faculty Dashboard</h2>
        <div class="list-group">
            <a href="advisingfac.php" class="list-group-item list-group-item-action">Advising Requests</a>
            <a href="selectgrade.php?faculty_id=<?php echo $faculty_id; ?>" class="list-group-item list-group-item-action">Select Grades</a>
        </div>
        
        <div class="mt-3">
            <a href="login.php" class="btn btn-secondary">Logout</a>
        </div>
    </div>
</body>
</html>