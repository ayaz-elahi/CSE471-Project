<?php

$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "edusystem";
$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);
if (!$conn) {
    die("Something went wrong;");
}

$id = "";
$course = "";
$section = "";
$status = "";

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $id = $_GET["id"];
    $sql = "SELECT * FROM advising WHERE id=$id";
    $result = $conn->query($sql);
    
    if ($result) {
        $row = $result->fetch_assoc();

        $id = $row["id"];
        $course = $row["course"];
        $section = $row["section"];
        $status = $row["status"];
    } else {
        $errorMessage = "Error fetching data: " . $conn->error;
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST["id"];
    $course = $_POST["course"];
    $section = $_POST["section"];
    $status = $_POST["status"];
    do {
        if (empty($id) || empty($course) || empty($section) || empty($status)) {
            $errorMessage = "All fields are required";
            break;
        }

        $sql = "
            UPDATE advising SET status = '$status' WHERE id = '$id';
            INSERT INTO student_grade (id, course, section) VALUES ('$id', '$course', '$section');
        ";

        if (mysqli_multi_query($conn, $sql)) {
            do {

                if ($result = mysqli_store_result($conn)) {
                    mysqli_free_result($result);
                }
            } while (mysqli_next_result($conn));

            $successMessage = "Course Approved";
        } else {
            $errorMessage = "Invalid query: " . $conn->error;
        }
        
    } while (false);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Student to your Advising Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container my-5">
        <h2>Make the Status "1" if you Approve</h2>

        <?php if (!empty($errorMessage)): ?>
            <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                <strong><?php echo $errorMessage; ?></strong>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">ID</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="id" value="<?php echo $id; ?>" readonly>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Course</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="course" value="<?php echo $course; ?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Section</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="section" value="<?php echo $section; ?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Status</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="status" value="<?php echo $status; ?>">
                </div>
            </div>

            <?php if (!empty($successMessage)): ?>
                <div class='alert alert-success alert-dismissible fade show' role='alert'>
                    <strong><?php echo $successMessage; ?></strong>
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                </div>
            <?php endif; ?>

            <div class="col-sm-3 d-grid">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>

    </div>
</body>
</html>
