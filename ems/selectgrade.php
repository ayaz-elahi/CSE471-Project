<?php
session_start();

// Make sure the user is logged in as faculty
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'faculty') {
    header("Location: login.php");
    exit;
}

// Get faculty ID from session
$logged_in_faculty_id = $_SESSION['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Grading Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container my-5">
        <h2>Student Grading Panel</h2>
        <p><strong>Faculty ID:</strong> <?php echo htmlspecialchars($logged_in_faculty_id); ?></p>
        <p class="text-muted">You can only grade students in courses assigned to your Faculty ID.</p>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Student ID</th>
                    <th>Course</th>
                    <th>Section</th>
                    <th>Current Grade</th>
                    <th>Action</th>
                </tr>
            </thead> 
            <tbody>
                <?php
                $servername = "localhost";
                $username = "root";
                $password = "";
                $database = "edusystem";

                // Database connection
                $connection = new mysqli($servername, $username, $password, $database);
                if ($connection->connect_error) {
                    die("Connection failed: " . $connection->connect_error);
                }

                // Query to get all students in courses taught by this specific faculty ID
                // This joins student_grade with facultysectionset based on course and section
                // and filters by the logged-in faculty's ID
                $sql = "
                    SELECT sg.id as student_id, sg.course, sg.section, sg.grade,
                           fs.faculty_id
                    FROM student_grade sg
                    INNER JOIN facultysectionset fs 
                    ON sg.course = fs.course AND sg.section = fs.section
                    WHERE fs.faculty_id = '$logged_in_faculty_id'
                    ORDER BY sg.course, sg.section, sg.id
                ";

                $result = $connection->query($sql);
                if (!$result) {
                    die("Invalid query: " . $connection->error);
                }

                // Check if any records found
                if ($result->num_rows > 0) {
                    // Display each student in courses taught by this faculty
                    while ($row = $result->fetch_assoc()) {
                        $grade_display = !empty($row['grade']) ? htmlspecialchars($row['grade']) : '<span class="text-muted">Not Graded</span>';
                        echo "
                        <tr>
                            <td>" . htmlspecialchars($row['student_id']) . "</td>
                            <td>" . htmlspecialchars($row['course']) . "</td>
                            <td>" . htmlspecialchars($row['section']) . "</td>
                            <td>" . $grade_display . "</td>
                            <td>
                                <a class='btn btn-primary btn-sm' href='addgrade.php?id=" . urlencode($row['student_id']) . "&course=" . urlencode($row['course']) . "&section=" . urlencode($row['section']) . "'>Update Grade</a>
                            </td>
                        </tr>
                        ";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center text-muted'>No students enrolled in your assigned courses yet.</td></tr>";
                }

                $connection->close();
                ?>
            </tbody>  
        </table>  
        
        <!-- Display courses assigned to this faculty -->
        <div class="mt-4">
            <h4>Your Assigned Courses</h4>
            <table class="table table-sm table-bordered">
                <thead class="table-secondary">
                    <tr>
                        <th>Course</th>
                        <th>Section</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Reconnect for second query
                    $connection = new mysqli($servername, $username, $password, $database);
                    
                    // Show which courses this faculty is assigned to
                    $courses_sql = "SELECT course, section FROM facultysectionset WHERE faculty_id = '$logged_in_faculty_id' ORDER BY course, section";
                    $courses_result = $connection->query($courses_sql);
                    
                    if ($courses_result && $courses_result->num_rows > 0) {
                        while ($course_row = $courses_result->fetch_assoc()) {
                            echo "
                            <tr>
                                <td>" . htmlspecialchars($course_row['course']) . "</td>
                                <td>" . htmlspecialchars($course_row['section']) . "</td>
                            </tr>
                            ";
                        }
                    } else {
                        echo "<tr><td colspan='2' class='text-center text-muted'>No courses assigned to you yet. Please contact admin.</td></tr>";
                    }
                    
                    $connection->close();
                    ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            <a href="faculty_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>