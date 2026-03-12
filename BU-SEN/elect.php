<?php
// Function to classify customer electricity band based on income
function classifyBand($income) {
    if ($income >= 10000000) {
        return "Band A - Wealthy";
    } elseif ($income >= 5000000) {
        return "Band B - Upper-middle Income";
    } elseif ($income >= 1000000) {
        return "Band C - Lower-middle Income";
    } else {
        return "Band D - Very Poor";
    }
}
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $name = $_POST['name'];
    $address = $_POST['address'];
    $income = $_POST['income'];
    $band = classifyBand($income);
}

$discoAvailable = rand(0,1);
$safeVoltage = true;  
$batteryLevel = rand(0,100);
$sunlight = rand(0,1);
$currentSource = "";
$chargingStatus = "";

if ($sunlight == 1){
    $currentSource = "Solar Power";
    
} elseif($discoAvailable == 1 && $safeVoltage){
    $currentSource = "DISCO/NEPA";

} elseif($batteryLevel >= 20){
    $currentSource = "Inverter Battery";
} else{
    $currentSource = "Low Battery";
}

if ($batteryLevel < 100){
    if($sunlight){
        $chargingStatus = "Charging inverter with solar";
    } elseif($discoAvailable){
        $chargingStatus = "Charging inverter with disco/NEPA";
    } else{
        $chargingStatus = "No charging source available";
    }
} else{
    $chargingStatus = "Inverter is fully charged";
}



// cost per hour in naira
$discoCostPerHour = 200; // Cost of DISCO per hour
$solarCostPerHour = 20; // Cost of Solar per hour
$inverterCostPerHour = 40; // Cost of inverter per hour

if ($currentSource == "DISCO/NEPA"){
    $currentCost = $discoCostPerHour;
} elseif($currentSource == "Solar Power"){
    $currentCost = $solarCostPerHour;
} else{
    $currentCost = $inverterCostPerHour;
}
$savings = $discoCostPerHour - $currentCost;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Electricity Distribution Band Classification</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
    
        <h2>Electricity Distribution System</h2>
        <form method="post">
            <label>Customer Name:</label><br>
            <input type="text" name="name" required><br><br>
            <label>Address:</label><br>
            <input type="text" name="address" required><br><br>
            <label>Annual Income (₦):</label><br>
            <input type="number" name="income" required><br><br>
            <input type="submit" value="Classify Customer">
        </form>
        <?php if(isset($band)) { ?>
        <hr>
        <h2>Customer Details</h2>

        <p>Name: <?php echo $name; ?><br></p>
        <p>Income: ₦<?php echo number_format($income); ?><br></p>
        <p>Economic Band: <?php echo $band; ?><br></p>

        <hr>

        <h2>Electricity Management System</h2>

        <p>Current Power Source: <?php echo $currentSource; ?><br></p>
        <p>Battery Level: <?php echo ($batteryLevel); ?>%<br></p>
        <p>Charging Status: <?php echo $chargingStatus; ?><br></p>

        <hr>

        <h2>Cost Analysis</h2>

        Current Cost per Hour: ₦<?php echo $currentCost; ?><br> 
        Estimated Savings: ₦<?php echo $savings; ?> per hour<br>

        <?php } ?>
    <div>
</body>
</html>