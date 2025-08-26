<?php
$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "edusystem";
$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);
if (!$conn) {
    die("Something went wrong;");
}

$role = "";
$name = "";
$email = "";
$password = "";
$id_value = "";

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST["role"];
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $id_value = $_POST["id_value"];

    do {
        if (empty($role) || empty($name) || empty($email) || empty($password) || empty($id_value)) {
            $errorMessage = "All fields are required";
            break;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        if ($role == "student") {
            $sql = "INSERT INTO students (student_id, name, email, password) VALUES ('$id_value', '$name', '$email', '$passwordHash')";
        } elseif ($role == "faculty") {
            $sql = "INSERT INTO faculty (faculty_id, name, email, password) VALUES ('$id_value', '$name', '$email', '$passwordHash')";
        } else {
            $sql = "INSERT INTO admins (admin_id, name, email, password) VALUES ('$id_value', '$name', '$email', '$passwordHash')";
        }

        $result = $conn->query($sql);

        if (!$result) {
            $errorMessage = "Error inserting data: " . $conn->error;
            break;
        }

        $successMessage = "Registration successful! You can now login.";
        $role = $name = $email = $password = $id_value = "";

    } while (false);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h2>Register</h2>

    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-warning"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success"><?php echo $successMessage; ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Role</label>
            <select class="form-control" name="role">
                <option value="student">Student</option>
                <option value="faculty">Faculty</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">ID</label>
            <input type="text" class="form-control" name="id_value" value="<?php echo $id_value; ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" class="form-control" name="name" value="<?php echo $name; ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="<?php echo $email; ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" name="password">
        </div>

        <button type="submit" class="btn btn-primary">Register</button>
        <a href="login.php" class="btn btn-link">Already have an account? Login</a>
    </form>
</div>
</body>
</html>
