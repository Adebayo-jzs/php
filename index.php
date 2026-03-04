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
// $batteryLevel = 100;
$batteryLevel = rand(0, 100);
$currentSource = "";
$chargingStatus = "";
// $isCharging = false;

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
        $isCharging = true;

    } elseif ($discoAvailable) {
        $chargingStatus = "Charging inverter with disco/NEPA";
        $isCharging = true;
    } else {
        $chargingStatus = "No charging source available";
        $isCharging = false;
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
<html lang="en">

<head>
    <title>PowerStats Dashboard | Electricity Management System</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="preconnect" href="https://fonts.googleapis.com"> -->
    <!-- <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet"> 
    <!-- <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script> -->
    <link rel="stylesheet" href="index.css">
</head>

<body>
    <div class="container corner-border">
        <div class="inner"></div>
        <header class="header-section"> 
            <h1>PowerStats Dashboard</h1>
            <!-- <p
                style="color: var(--text-secondary); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 2px;">
                Last Update: <?php echo date("d/m/Y H:i"); ?>
            </p> -->
        </header>

        <!-- <form method="post">
            <div class="form-group">
                <label>Customer Name</label>
                <input type="text" name="name" placeholder="John Doe" required>
            </div>
            <div class="form-group">
                <label>Annual Income (₦)</label>
                <input type="number" name="income" placeholder="Enter annual income" required>
            </div>
            <input type="submit" value="Analyze Grid Profile">
        </form> -->

        <?php if (isset($band)): ?>

            <section>
                <h2>Customer Details</h2>
                <div class="dashboard-grid">

                    <div class="card corner-border">
                        <div class="inner"></div>
                        <div class="card-title">Customer Name</div>
                        <div class="card-value"><?php echo strtoupper(htmlspecialchars($name)); ?></div>
                    </div>
                    <div class="card corner-border">
                        <div class="inner"></div>
                        <div class="card-title">Electricity Band</div>
                        <div class="card-value"><?php echo strtoupper($band); ?></div>
                        <p
                            style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 0.5rem; font-family: var(--font-mono);">
                            Income: ₦<?php echo number_format($income); ?>
                        </p>
                    </div>
                </div>
            </section>

            <section>
                <h2>Electricity Management System</h2>
                <div class="dashboard-grid">
                    <div class="card corner-border">
                        <div class="inner"></div>
                        <div class="card-title">Current Source</div>
                        <div class="card-value"><?php echo strtoupper($currentSource); ?></div>
                    </div>
                    <div class="card corner-border">
                        <div class="inner"></div>
                        <?php
                        if ($batteryLevel <= 20) {
                            $color = "red";
                        } elseif ($batteryLevel <= 50) {
                            $color = "orange";
                        } else {
                            $color = "green";
                        }

                        ?>
                        <div class="flex items-center gap-4">

                            <div class="battery-bars" style="border-color:<?php echo $color ?>;">
                                <div class="battery-bar-full"
                                    style="background-color:<?php echo $color ?>; height:<?php echo $batteryLevel ?>%;">
                                </div>
                                <!-- Top Cap -->
                                <div class="top-cap" style="border-color:<?php echo $color ?>;"></div>
                            </div>
                            <div>
                                <div style="font-size: 2.5rem; font-weight: 400; color: var(--text-primary); padding:0;">
                                    <?php echo $batteryLevel; ?>%
                                </div>
                                <div class="card-title">Battery Level</div>
                                <div
                                    class="status-badge <?php echo $isCharging ? 'status-warning' : ($batteryLevel == 100 ? 'status-success' : 'status-danger'); ?>">
                                    <?php echo strtoupper($chargingStatus); ?>
                                </div>
                            </div>
                            <!-- <div style="font-size: 2.5rem; font-weight: 400; color: var(--text-primary); margin-bottom: 1rem;">
                            <?php echo $batteryLevel; ?>%
                            </div> -->
                        </div>

                    </div>
                </div>
            </section>

            <section>
                <h2>Cost Analysis
                </h2>
                <div class="dashboard-grid">
                    <div class="card corner-border">
                        <div class="inner"></div>
                        <div class="card-title">Operational Cost / HR</div>
                        <div class="card-value">₦<?php echo number_format($currentCost); ?>.00</div>
                    </div>
                    <div class="card corner-border">
                        <div class="inner"></div> 
                        <div class="card-title">Cost Savings / HR</div>
                        <div class="card-value" style="color: var(--success);">+₦<?php echo number_format($savings); ?>.00
                        </div>
                    </div>
                </div>
            </section>

        <?php else: ?>
            <form method="post">
                <div class="form-group">
                    <label>Customer Name</label>
                    <input type="text" name="name" placeholder="John Doe" required>
                </div>
                <div class="form-group">
                    <label>Annual Income (₦)</label>
                    <input type="number" name="income" placeholder="Enter annual income" required>
                </div>
                <input type="submit" value="Analyze Grid Profile">
            </form>
        <?php endif; ?>
    </div>
</body>

</html>