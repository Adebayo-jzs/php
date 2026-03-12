<?php
// Function to classify customer electricity band based on income
function classifyBand($income)
{
    if ($income >= 5000000) { // If income is 5 million or more
        return "Band A - Wealthy"; // Return Band A
    } elseif ($income >= 2000000) { // If income is between 2M and 4.9M
        return "Band B - Upper-Middle Income"; // Return Band B
    } elseif ($income >= 800000) { // If income is between 800k and 1.9M
        return "Band C - Lower-Middle Income"; // Return Band C
    } else { // If income is below 800k
        return "Band D - Very Poor"; // Return Band D
    }
}
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name']; // Get customer name from form
    $income = $_POST['income']; // Get income from form
    $band = classifyBand($income); // Call function to determine band
}

// rand(0,1) generates either a random integer between 0 or 1
// 1 means power is available, 0 means not available
$discoAvailable = rand(0, 1);
$safeVoltage = rand(0, 1);
$sunlight = rand(0, 1);
// rand(0,100) generates either a random integer between 0 or 100 for battery percentage
$batteryLevel = rand(0, 100);
$currentSource = "";
$chargingStatus = "";

if ($discoAvailable == 1 && $safeVoltage == 1) {
    $currentSource = "DISCO/NEPA";
} elseif ($sunlight == 1) {
    $currentSource = "Solar Power";

} elseif ($batteryLevel > 15) {
    $currentSource = "Inverter Battery";
} else {
    $currentSource = "Low Battery - no power source is available";
}

if ($batteryLevel < 100) {
    if ($sunlight) {
        $chargingStatus = "Charging inverter with solar";
    } elseif ($discoAvailable) {
        $chargingStatus = "Charging inverter with disco/NEPA";
    } else {
        $chargingStatus = "No charging source available";
    }
} else {
    $chargingStatus = "Inverter is fully charged";
}



// cost per hour in naira
$discoCostPerHour = 200; // Cost of DISCO per hour
$solarCostPerHour = 20; // Cost of Solar per hour
$inverterCostPerHour = 40; // Cost of inverter per hour

if ($currentSource == "DISCO/NEPA") {
    $currentCost = $discoCostPerHour;
} elseif ($currentSource == "Solar Power") {
    $currentCost = $solarCostPerHour;
} else {
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
    <link rel="stylesheet" href="old.css">
</head>

<body style="font-family: monospace; display: flex; justify-content: center; align-items: center; padding-top:20px;">
    <div class="container">

        <h2>Electricity Distribution & Band Classification System</h2>
        <form method="post">
            <div>
                <label>Customer Name:</label>
                <input type="text" name="name" required style="margin-bottom:12px; width: 573px;">
            </div>
            <div>
                <label>Annual Income (₦):</label>
                <input type="number" name="income" required style="margin-bottom:12px; width: 573px;">
            </div>
            <input type="submit" value="Submit">
        </form>
        <?php if (isset($band)) { ?>
            <hr>
            <div class="background-color:#faf9f5;">
                <h2>Customer Details</h2>
                <p>Name: <?php echo $name; ?></p>
                <p>Annual Income: ₦<?php echo number_format($income); ?></p>
                <p>Economic Band: <?php echo $band; ?></p>
            </div>

            <hr>

            <h2>Electricity Management System</h2>

            <p>Current Power Source: <?php echo $currentSource; ?></p>
            <p>Battery Level: <?php echo ($batteryLevel); ?>%</p>
            <p>Charging Status: <?php echo $chargingStatus; ?></p>

            <hr>

            <h2>Cost Analysis</h2>

            <p>Current Cost per Hour: ₦<?php echo $currentCost; ?></p>
            <p>Estimated Savings: ₦<?php echo $savings; ?> per hour</p>

        <?php } ?>
        <div>
</body>

</html>