<?php
echo '<br><h1>Students Record</h1>';
$dbconnect = mysqli_connect('localhost', 'root', '', 'students')
    or die('Database connection failed');

$query = "SELECT * FROM `hallrecords`";
$result = mysqli_query($dbconnect, $query);
$row = mysqli_fetch_array($result);
$no = 1;
?>
<html>
<head>
    <title>Hall Registration System</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="view.css">
</head>
<body>
<div class="table-container">
<table  border="1" cellpadding="10">
        <tr>
            <th>SN</th>
            <th>Matric No.</th>
            <th>Fullname</th>
            <th>Hall</th>
            <th>Department</th>
            <th>Gender</th>
            <th>Level</th>
            <th>Age</th>
        </tr>
        <?php
            do { 
            echo "<tr>
            <td>$row[0]</td>
            <td>$row[1]</td>
            <td>$row[2]</td>
            <td>$row[3]</td>
            <td>$row[4]</td>
            <td>$row[5]</td>
            <td>$row[6]</td>
            <td>$row[7]</td>
            </tr>";

            } while ($row = mysqli_fetch_array($result));
        ?>
</table>
</div>
</body>
</html>