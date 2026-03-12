<?php

$conn = new mysqli("localhost","root","","students");

if($conn->connect_error){
    die("Connection failed: ".$conn->connect_error);
}

// Fetch all records
$result = $conn->query("SELECT * FROM hallrecords ORDER BY sn DESC");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Registered Students</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="view.css">
</head>
<body>
<div>
<h1>All Registered Students</h1>

<div class="table-container">

    <table border="1" cellpadding="10">
        <thead>

            <tr>
                <th>ID</th>
                <th>Fullname</th>
                <th>Matric</th>
                <th>Hall</th>
                <th>Department</th>
                <th>Level</th>
                <th>Gender</th>
                <th>Age</th>
            </tr>
        </thead>
        <tbody>
        <?php

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        
        echo "<tr>
        <td>{$row['sn']}</td>
        <td>{$row['fullname']}</td>
        <td>{$row['matric']}</td>
        <td>{$row['hall']}</td>
        <td>{$row['department']}</td>
        <td>{$row['level']}</td>
        <td>{$row['gender']}</td>
        <td>{$row['age']}</td>
        </tr>";
        }
        } else {
            echo "<tr><td colspan='8'>No records found</td></tr>";
            }
            
            $conn->close();
            
            ?>
        </tbody>
</table>
</div>

<!-- <br> -->
<!-- <a href="register.php">⬅ Back to Registration</a> -->
</div>
</body>
</html>