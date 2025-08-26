<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advising</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container my-5">
        <h2>All courses</h2>
        
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Course</th>
                    <th>Section</th>
                    <th>Status</th> 
                </tr>
            </thead> 
            <tbody>
                <?php
                $servername = "localhost";
                $username = "root";
                $password = "";
                $database = "edusystem";


                $connection = new mysqli($servername, $username, $password, $database);
                if ($connection->connect_error) {
                    die("Connection failed: " . $connection->connect_error);
                }


                $sql = "SELECT * FROM advising";
                $result = $connection->query($sql);
                if (!$result) {
                    die("Invalid query: " . $connection->error);
                }


                while ($row = $result->fetch_assoc()) {
                    echo "
                    <tr>
                        <td>{$row['id']}</td>
                        <td>{$row['course']}</td>
                        <td>{$row['section']}</td>
                        <td>{$row['status']}</td>
                        <td>
                            <a class='btn btn-primary btn-sm' href='advisingapprv.php?id={$row['id']}'>Approve</a>
                        </td>
                    </tr>
                    ";
                }
                ?>
            </tbody>  
        </table>  
    </div>
</body>
</html>
