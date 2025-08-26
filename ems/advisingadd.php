<?php
session_start();

// Make sure the user is logged in as student
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit;
}

$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "edusystem";
$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);
if (!$conn) {
    die("Something went wrong;");
}

// Get student ID from session
$student_id = $_SESSION['id'];
$course = "";
$section = "";
$status = "0";
$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $course = $_POST["course"];
    $section = $_POST["section"];
    $status = "0";  

    do {
        if (empty($course) || empty($section)) {
            $errorMessage = "You must provide Course and Section";
            break;
        }
        
        // Check if this course/section combination exists in facultysectionset
        $check_sql = "SELECT * FROM facultysectionset WHERE course='$course' AND section='$section'";
        $check_result = $conn->query($check_sql);
        
        if ($check_result->num_rows == 0) {
            $errorMessage = "This course and section combination is not available. Please check available courses.";
            break;
        }
        
        // Check if student has already applied for this course/section
        $duplicate_sql = "SELECT * FROM advising WHERE id='$student_id' AND course='$course' AND section='$section'";
        $duplicate_result = $conn->query($duplicate_sql);
        
        if ($duplicate_result->num_rows > 0) {
            $errorMessage = "You have already applied for this course and section.";
            break;
        }
        
        $sql = "INSERT INTO advising(id, course, section, status) VALUES ('$student_id', '$course', '$section', '$status')";
        $result = $conn->query($sql);

        if (!$result) {
            $errorMessage = "Error inserting data: " . $conn->error;
            break;
        }

        $course = "";
        $section = "";

        $successMessage = "Your advising request has been received, please wait for your Advisor's approval!";

    } while (false);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Advising Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container my-5">
        <h2>Course Advising Request</h2>
        <p><strong>Student ID:</strong> <?php echo htmlspecialchars($student_id); ?></p>

        <?php
        if (!empty($errorMessage)){
            echo "
            <div class='alert alert-warning alert-dismissible fade show' role='alert'>
            <strong>$errorMessage</strong>
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
            ";
        }
        ?>

        <form method="post">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Course</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="course" value="<?php echo htmlspecialchars($course);?>" placeholder="e.g., CSE101">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Section</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="section" value="<?php echo htmlspecialchars($section);?>" placeholder="e.g., A">
                </div>
            </div>

        <?php
        if (!empty($successMessage)){
            echo "
            <div class='row mb-3'>
                <div class='offset-sm-3 col-sm-6'>
                    <div class='alert alert-success alert-dismissible fade show' role='alert'>
                        <strong>$successMessage</strong>
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>
                 </div>
            </div>
            ";
        }
        ?>

            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-3 d-grid">
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </div>
        </form>
        
        <div class="mt-3">
            <a href="showcourses.php" class="btn btn-info">View Available Courses</a>
            <a href="student_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

    </div>
</body>
</html>