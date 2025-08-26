<?php
session_start();

// Make sure the user is logged in and is a student
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['id']; // Student ID from login

$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "edusystem";

$connection = new mysqli($hostName, $dbUser, $dbPassword, $dbName);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Fetch grades for this specific student using their logged-in ID
$sql = "SELECT * FROM student_grade WHERE id = '$student_id'";
$result = $connection->query($sql);
if (!$result) {
    die("Invalid query: " . $connection->error);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Grades</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h2>My Grades - Student ID: <?php echo htmlspecialchars($student_id); ?></h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Course</th>
                <th>Section</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $grade_display = !empty($row['grade']) ? htmlspecialchars($row['grade']) : 'Not Graded Yet';
                    echo "
                    <tr>
                        <td>" . htmlspecialchars($row['course']) . "</td>
                        <td>" . htmlspecialchars($row['section']) . "</td>
                        <td>" . $grade_display . "</td>
                    </tr>
                    ";
                }
            } else {
                echo "<tr><td colspan='3' class='text-center'>No courses enrolled yet. Please complete advising process first.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="mt-3">
        <a href="student_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>
</body>
</html>

<?php $connection->close(); ?>