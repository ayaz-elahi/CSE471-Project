<?php
session_start();

// Make sure the user is logged in as faculty
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'faculty') {
    header("Location: login.php");
    exit;
}

$faculty_id = $_SESSION['id'];

$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "edusystem";
$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);
if (!$conn) {
    die("Something went wrong;");
}

$student_id = "";
$course = "";
$section = "";
$grade = "";

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $student_id = isset($_GET["id"]) ? $_GET["id"] : "";
    $course = isset($_GET["course"]) ? $_GET["course"] : "";
    $section = isset($_GET["section"]) ? $_GET["section"] : "";
    
    if (empty($student_id)) {
        $errorMessage = "Student ID is required.";
    } else {
        // Verify that this faculty can grade this student's course
        $verify_sql = "
            SELECT sg.id, sg.course, sg.section, sg.grade, fs.faculty_id
            FROM student_grade sg
            INNER JOIN facultysectionset fs 
            ON sg.course = fs.course AND sg.section = fs.section
            WHERE sg.id = '$student_id' AND fs.faculty_id = '$faculty_id'
        ";
        
        // If course and section are provided, add them to the query for extra verification
        if (!empty($course) && !empty($section)) {
            $verify_sql .= " AND sg.course = '$course' AND sg.section = '$section'";
        }
        
        $result = $conn->query($verify_sql);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $student_id = $row["id"];
            $course = $row["course"];
            $section = $row["section"];
            $grade = $row["grade"];
        } else {
            $errorMessage = "Access denied. This student is not enrolled in any of your assigned courses.";
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST["id"];
    $course = $_POST["course"];
    $section = $_POST["section"];
    $grade = $_POST["grade"];
    
    do {
        if (empty($student_id) || empty($course) || empty($section) || empty($grade)) {
            $errorMessage = "All fields are required";
            break;
        }
        
        // Double-check faculty has permission to grade this student
        $verify_sql = "
            SELECT sg.id 
            FROM student_grade sg
            INNER JOIN facultysectionset fs 
            ON sg.course = fs.course AND sg.section = fs.section
            WHERE sg.id = '$student_id' AND fs.faculty_id = '$faculty_id' 
            AND sg.course = '$course' AND sg.section = '$section'
        ";
        
        $verify_result = $conn->query($verify_sql);
        
        if (!$verify_result || $verify_result->num_rows == 0) {
            $errorMessage = "Access denied. You don't have permission to grade this student for this course.";
            break;
        }

        $sql = "UPDATE student_grade SET grade = '$grade' WHERE id = '$student_id' AND course = '$course' AND section = '$section'";
        $result = $conn->query($sql);

        if (!$result) {
            $errorMessage = "Invalid query: " . $conn->error;
            break;
        }

        $successMessage = "Grade updated successfully for Student ID: $student_id in $course-$section";
    } while (false);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Student Grade</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container my-5">
        <h2>Update Student Grade</h2>
        <div class="mb-3">
            <p><strong>Faculty ID:</strong> <?php echo htmlspecialchars($faculty_id); ?></p>
            <p class="text-muted">You can only grade students in courses assigned to your Faculty ID.</p>
        </div>

        <?php if (!empty($errorMessage)): ?>
            <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                <strong><?php echo htmlspecialchars($errorMessage); ?></strong>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($successMessage)): ?>
            <div class='alert alert-success alert-dismissible fade show' role='alert'>
                <strong><?php echo htmlspecialchars($successMessage); ?></strong>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
        <?php endif; ?>

        <?php if (empty($errorMessage) && !empty($student_id)): ?>
        <form method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($student_id); ?>">
            <input type="hidden" name="course" value="<?php echo htmlspecialchars($course); ?>">
            <input type="hidden" name="section" value="<?php echo htmlspecialchars($section); ?>">
            
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Student ID</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($student_id); ?>" readonly>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Course</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($course); ?>" readonly>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Section</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($section); ?>" readonly>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Grade</label>
                <div class="col-sm-6">
                    <select class="form-select" name="grade" required>
                        <option value="">Select Grade</option>
                        <option value="A+" <?php if($grade == "A+") echo "selected"; ?>>A+</option>
                        <option value="A" <?php if($grade == "A") echo "selected"; ?>>A</option>
                        <option value="A-" <?php if($grade == "A-") echo "selected"; ?>>A-</option>
                        <option value="B+" <?php if($grade == "B+") echo "selected"; ?>>B+</option>
                        <option value="B" <?php if($grade == "B") echo "selected"; ?>>B</option>
                        <option value="B-" <?php if($grade == "B-") echo "selected"; ?>>B-</option>
                        <option value="C+" <?php if($grade == "C+") echo "selected"; ?>>C+</option>
                        <option value="C" <?php if($grade == "C") echo "selected"; ?>>C</option>
                        <option value="C-" <?php if($grade == "C-") echo "selected"; ?>>C-</option>
                        <option value="D+" <?php if($grade == "D+") echo "selected"; ?>>D+</option>
                        <option value="D" <?php if($grade == "D") echo "selected"; ?>>D</option>
                        <option value="F" <?php if($grade == "F") echo "selected"; ?>>F</option>
                        <option value="I" <?php if($grade == "I") echo "selected"; ?>>I (Incomplete)</option>
                        <option value="W" <?php if($grade == "W") echo "selected"; ?>>W (Withdraw)</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-3 d-grid">
                    <button type="submit" class="btn btn-primary">Update Grade</button>
                </div>
            </div>
        </form>
        <?php endif; ?>
        
        <div class="mt-3">
            <a href="selectgrade.php?faculty_id=<?php echo htmlspecialchars($faculty_id); ?>" class="btn btn-secondary">Back to Grading</a>
            <a href="faculty_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

    </div>
</body>
</html>