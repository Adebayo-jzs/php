<?php
$conn = new mysqli("localhost","root","","students");
if($conn->connect_error){
    die("Connection failed: ".$conn->connect_error);
}
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = $_POST['fullname'];
    $matric = $_POST['matric'];
    $hall = $_POST['hall'];
    $department = $_POST['department'];
    $level = $_POST['level'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];

    $sql = "INSERT INTO hallrecords(fullname,matric,hall,department,level,gender,age)
            VALUES('$fullname','$matric','$hall','$department','$level','$gender','$age')";
    
    if($conn->query($sql) === TRUE){
        $message = "Record addded successfully";
    } else {
        $message = "Error: ".$conn->error;
    }
} 
?>

<html>
<head>
    <title>Hall Registration System</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">

    <h1>Student Hall Registration Site</h1>
    <form method="POST">
        <div>
            <label>Fullname</label>
            <input required type="text" name="fullname" placeholder="John Doe">
        </div>
        <div>
            <label>Matric No</label>
            <input required type="text" name="matric" placeholder="BU24/0000">
        </div>
        <div>
            <label>Hall</label>
            <input required type="text" name="hall" placeholder="Winslow">
        </div>
        <div>
            <label>Department:</label>
            <input required type="text" name="department" placeholder="Information Technology">
        </div>
        <div class="flex-inputs">
            <div>
                <label>Level:</label>
                <input required type="number" name="level" placeholder="200">
            </div>
            <div>
                <label>Gender:</label>
                <input required type="text" name="gender" placeholder="M">
            </div>
            <div>
                <label>Age:</label>
                <input required type="number" name="age" placeholder="12">
            </div>
        </div>

        <input type="submit" value="Submit">
        <p>
            <?php echo $message; ?>
        </p>
    </form>
    
     
</div>
</body>

</html>