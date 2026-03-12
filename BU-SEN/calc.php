<?php
// Electricity Distribution Band Classification System

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Collect customer data
    $name = $_POST["name"];
    $income = $_POST["income"];

    // Determine band based on income
    if ($income >= 5000000) {
        $band = "Band A – Wealthy";
    } elseif ($income >= 2000000) {
        $band = "Band B – Upper-Middle Income";
    } elseif ($income >= 500000) {
        $band = "Band C – Lower-Middle Income";
    } else {
        $band = "Band D – Very Poor";
    }

    // Display result
    echo "<h2>Customer Information</h2>";
    echo "Name: " . $name . "<br>";
    echo "Annual Income: ₦" . number_format($income) . "<br>";
    echo "Assigned Band: <strong>" . $band . "</strong>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Electricity Distribution Band System</title>
</head>
<body>

<h2>Electricity Distribution Classification</h2>

<form method="post">
    Customer Name:<br>
    <input type="text" name="name" required><br><br>

    Annual Income (₦):<br>
    <input type="number" name="income" required><br><br>

    <input type="submit" value="Submit">
</form>

</body>
</html>
    