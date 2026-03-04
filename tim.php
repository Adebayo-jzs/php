<?php
// Function to classify customer electricity band based on income
function classifyBand($income)
{
    if ($income >= 5000000) {
        return "Band A - Wealthy";
    } elseif ($income >= 2000000) {
        return "Band B - Upper-Middle Income";
    } elseif ($income >= 800000) {
        return "Band C - Lower-Middle Income";
    } else {
        return "Band D - Low Income";
    }
}

// Initialize form variables
$name = "";
$income = 0;
$band = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $income = filter_input(INPUT_POST, 'income', FILTER_VALIDATE_INT);
    $band = classifyBand($income);
}

// ─── SIMULATE POWER CONDITIONS ───────────────────────────────────────────────

// Power source availability (1 = available, 0 = not available)
$discoAvailable = rand(0, 1);
$sunlight = rand(0, 1);
$generatorFuel = rand(0, 1); // 1 = generator has fuel, 0 = no fuel

// Voltage simulation: DISCO voltage between 160V and 260V (safe range: 180V–240V)
$discoVoltage = rand(160, 260);
$safeVoltageMin = 180;
$safeVoltageMax = 240;
$safeVoltage = ($discoVoltage >= $safeVoltageMin && $discoVoltage <= $safeVoltageMax);

// Battery & solar levels (percentage)
$batteryLevel = rand(0, 100); // Inverter battery level
$solarOutput = rand(0, 100); // Solar panel output level

// Generator status
$generatorOn = false;
$generatorReason = "";

// ─── CHANGEOVER LOGIC ────────────────────────────────────────────────────────

$currentSource = "";
$changeoverNote = "";

// PRIORITY 1 — DISCO is default if available and voltage is safe
if ($discoAvailable == 1 && $safeVoltage) {
    $currentSource = "DISCO/NEPA";
    $changeoverNote = "DISCO supply is stable (Voltage: {$discoVoltage}V). Running on DISCO.";

    // PRIORITY 2 — DISCO available but voltage is unsafe → switch to inverter or solar
} elseif ($discoAvailable == 1 && !$safeVoltage) {
    $changeoverNote = "DISCO voltage is unsafe ({$discoVoltage}V). Switching away from DISCO.";

    if ($solarOutput > 18 && $sunlight == 1) {
        $currentSource = "Solar Power";
        $changeoverNote .= " Switched to Solar Power (Output: {$solarOutput}%).";
    } elseif ($batteryLevel > 18) {
        $currentSource = "Inverter Battery";
        $changeoverNote .= " Switched to Inverter Battery (Level: {$batteryLevel}%).";
    } elseif ($generatorFuel == 1) {
        $currentSource = "Generator";
        $generatorOn = true;
        $generatorReason = "DISCO voltage unsafe & solar/inverter too low.";
        $changeoverNote .= " Generator activated — solar and inverter levels too low.";
    } else {
        $currentSource = "No Power Source Available";
        $changeoverNote .= " No fallback available. Generator has no fuel.";
    }

    // PRIORITY 3 — No DISCO → try solar first
} elseif ($sunlight == 1 && $solarOutput > 18) {
    $currentSource = "Solar Power";
    $changeoverNote = "No DISCO supply. Running on Solar Power (Output: {$solarOutput}%).";

    // PRIORITY 4 — No solar → try inverter
} elseif ($batteryLevel > 18) {
    $currentSource = "Inverter Battery";
    $changeoverNote = "No DISCO or Solar. Running on Inverter Battery (Level: {$batteryLevel}%).";

    // PRIORITY 5 — Solar/inverter below 18% → switch to generator
} elseif ($generatorFuel == 1) {
    $currentSource = "Generator";
    $generatorOn = true;
    $generatorReason = "Solar output ({$solarOutput}%) and inverter level ({$batteryLevel}%) are both below 18%.";
    $changeoverNote = "Solar and Inverter too low. Generator activated.";

    // PRIORITY 6 — Nothing available
} else {
    $currentSource = "No Power Source Available";
    $changeoverNote = "All power sources unavailable or too low. No fuel for generator.";
}

// ─── CHARGING STATUS ─────────────────────────────────────────────────────────

$chargingStatus = "";
$isCharging = false;

if ($batteryLevel < 100) {
    if ($currentSource == "DISCO/NEPA" || ($discoAvailable == 1 && $safeVoltage)) {
        $chargingStatus = "Charging inverter with DISCO/NEPA";
        $isCharging = true;
    } elseif ($sunlight == 1) {
        $chargingStatus = "Charging inverter with Solar";
        $isCharging = true;
    } elseif ($generatorOn) {
        $chargingStatus = "Charging inverter with Generator";
        $isCharging = true;
    } else {
        $chargingStatus = "No charging source available";
        $isCharging = false;
    }
} else {
    $chargingStatus = "Inverter is fully charged";
}

// ─── COST CALCULATION ────────────────────────────────────────────────────────

$discoCostPerHour = 200; // ₦ per hour
$solarCostPerHour = 20;
$inverterCostPerHour = 40;
$generatorCostPerHour = 150; // Generator fuel cost per hour

switch ($currentSource) {
    case "DISCO/NEPA":
        $currentCost = $discoCostPerHour;
        break;
    case "Solar Power":
        $currentCost = $solarCostPerHour;
        break;
    case "Inverter Battery":
        $currentCost = $inverterCostPerHour;
        break;
    case "Generator":
        $currentCost = $generatorCostPerHour;
        break;
    default:
        $currentCost = 0;
}

$savings = max(0, $discoCostPerHour - $currentCost);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>PowerStats Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link rel="stylesheet" href="index.css">
</head>

<body>
    <div class="container corner-border">
        <div class="inner"></div>
        <header class="header-section">
            <h1>PowerStats Dashboard <span style="font-size: 0.6rem; vertical-align: middle; opacity: 0.5;">v2.0</span>
            </h1>
        </header>

        <?php if (!empty($band)): ?>

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
                            $color = "var(--danger)";
                        } elseif ($batteryLevel <= 50) {
                            $color = "var(--warning)";
                        } else {
                            $color = "var(--success)";
                        }
                        ?>
                        <div class="flex items-center gap-4">
                            <div class="battery-bars" style="border-color:<?php echo $color ?>;">
                                <div class="battery-bar-full"
                                    style="background-color:<?php echo $color ?>; height:<?php echo $batteryLevel ?>%;">
                                </div>
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
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <h2>System Diagnostics</h2>
                <div class="dashboard-grid">
                    <div class="card corner-border">
                        <div class="inner"></div> 
                        <div class="card-title">DISCO Voltage</div>
                        <div class="card-value"
                            style="font-size: 1.2rem; <?php echo $safeVoltage ? 'color: var(--success);' : 'color: var(--danger);'; ?>">
                            <?php echo $discoVoltage; ?>V
                            <span
                                style="font-size: 0.7rem; display: block; color: var(--text-secondary);"><?php echo $safeVoltage ? '(SAFE)' : '(UNSAFE)'; ?></span>
                        </div>
                    </div>
                    <div class="card corner-border">
                        <div class="inner"></div> 
                        <div class="card-title">Solar Output</div>
                        <div class="card-value" style="font-size: 1.2rem;"><?php echo $solarOutput; ?>%</div>
                    </div>
                    <div class="card corner-border" style="grid-column: span 2;">
                        <div class="inner"></div>
                        <div class="card-title">LOG</div>
                        <div class="card-value"
                            style="font-size: 0.85rem; font-family: var(--font-mono); color: var(--text-secondary); border-left: 2px solid var(--border); padding-left: 1rem; margin-top: 0.5rem;">
                            > <?php echo $changeoverNote; ?><br>
                            <?php if ($generatorOn): ?>
                                <span style="color: var(--warning);">> GENERATOR: ACTIVE
                                    (<?php echo $generatorReason; ?>)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <h2>Cost Analysis</h2>
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