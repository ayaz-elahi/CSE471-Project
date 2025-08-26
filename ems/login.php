<?php
session_start();

$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "edusystem";

$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);
if (!$conn) die("Connection failed: " . $conn->connect_error);

$email = $password = $role = "";
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $role = $_POST["role"];

    if (empty($email) || empty($password) || empty($role)) {
        $errorMessage = "All fields are required";
    } else {
        if ($role == "student") {
            $sql = "SELECT * FROM students WHERE email='$email'";
        } else if ($role == "faculty") {
            $sql = "SELECT * FROM faculty WHERE email='$email'";
        } else if ($role == "admin") {
            $sql = "SELECT * FROM admins WHERE email='$email'";
        } else {
            $errorMessage = "Invalid role";
        }

        if (empty($errorMessage)) {
            $result = $conn->query($sql);
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                if (password_verify($password, $row['password'])) {
                    $_SESSION['role'] = $role;
                    if ($role == "student") $_SESSION['id'] = $row['student_id'];
                    if ($role == "faculty") $_SESSION['id'] = $row['faculty_id'];
                    if ($role == "admin") $_SESSION['id'] = $row['admin_id'];

                    // Redirect to appropriate dashboard
                    if ($role == "student") {
                        header("Location: student_dashboard.php");
                    } else if ($role == "faculty") {
                        header("Location: faculty_dashboard.php");
                    } else if ($role == "admin") {
                        header("Location: admin_dashboard.php");
                    }
                    exit;
                } else {
                    $errorMessage = "Invalid password";
                }
            } else {
                $errorMessage = "No account found with this email and role";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Education Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body class="bg-light">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mt-5">
                <div class="card-header">
                    <h3 class="text-center">Education Management System</h3>
                    <h5 class="text-center text-muted">Login</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($errorMessage)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong><?php echo htmlspecialchars($errorMessage); ?></strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input class="form-control" type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input class="form-control" type="password" id="password" name="password" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="role" class="form-label">Login as</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">Select Your Role</option>
                                <option value="student" <?php if($role=="student") echo "selected";?>>Student</option>
                                <option value="faculty" <?php if($role=="faculty") echo "selected";?>>Faculty</option>
                                <option value="admin" <?php if($role=="admin") echo "selected";?>>Admin</option>
                            </select>
                        </div>
                        
                        <div class="d-grid">
                            <button class="btn btn-primary" type="submit">Login</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p class="mb-0">Don't have an account? <a href="register.php">Register here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>