<?php

$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "edusystem";
$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);
if (!$conn) {
    die("Something went wrong;");
}

$faculty_id="";
$course = "";
$section = "";

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $faculty_id=$_POST["faculty_id"];
    $course= $_POST["course"];
    $section= $_POST["section"];
  

    do {
        if (empty($course) || empty($section)) {
            $errorMessage = "You must provide Course and Section";
            break;
        }
        
        $sql = "INSERT INTO facultysectionset(faculty_id,course, section) VALUES ('$faculty_id','$course', '$section')";
        $result = $conn->query($sql);

        if (!$result) {
            $errorMessage = "Error inserting data: " . $conn->error;
            break;
        }

        $faculty_id="";
        $course = "";
        $section = "";


        $successMessage = "New course / New section added";
        //header e admin er dashboard er link thaakbe
        header("");
        exit;

    } while (false);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add new courses and/or sections</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container my-5">
        <h2>Please add the Course and Section</h2>

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
                <label class="col-sm-3 col-form-label">Faculty ID</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="faculty_id" value="<?php echo $faculty_id;?>">
                </div>
            </div>            
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Course</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="course" value="<?php echo $course;?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Section</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="section" value="<?php echo $section;?>">
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
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </div>
        </form>

    </div>
</body>
</html>
