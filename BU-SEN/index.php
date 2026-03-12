<?php
$band = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"] ?? "";
    $income = $_POST["income"] ?? 0;

    // $income = 300000;
    if ($income >= 5000000) {
        $band = 'Band A – Wealthy';
    } elseif ($income >= 2000000) {
        $band = 'Band B – Upper-Middle Income';
    } elseif ($income >= 500000) {
        $band = 'Band C – Lower-Middle Income';
    } else {
        $band = 'Band D – Very Poor';
    }
    $monthly_income = $income / 12;
    // echo "Band $band";
    echo "
        <h2>Name: $name<br></h2>
        <h2>Distribution: $band</h2>
        <h2>Monthly income: $monthly_income </h2>
    ";
}
?>

<html>
<head>
    <title>Electricity Distribution Band System</title>
</head>
<body>

    <form method="post">
        Name: <input type="text" name="name"><br>
        Annual Income(₦): <input type="number" name ="income"><br>
        <input type="submit" value="submit">
    </form>
    
    <!-- <h2>Name: <?php echo $_POST["name"] ?? ''; ?><br></h2> -->
    <!-- Your email address is: <?php echo $_POST["email"] ?? ''; ?> -->
    <!-- <h2>Distribution: <?php echo "Band $band" ?></h2> -->

</body>

</html>