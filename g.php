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
    $name   = htmlspecialchars(trim($_POST['name']));
    $income = filter_input(INPUT_POST, 'income', FILTER_VALIDATE_INT);
    $band   = classifyBand($income);
}

// ─── SIMULATE POWER CONDITIONS ───────────────────────────────────────────────

// Power source availability (1 = available, 0 = not available)
$discoAvailable   = rand(0, 1);
$sunlight         = rand(0, 1);
$generatorFuel    = rand(0, 1); // 1 = generator has fuel, 0 = no fuel

// Voltage simulation: DISCO voltage between 160V and 260V (safe range: 180V–240V)
$discoVoltage     = rand(160, 260);
$safeVoltageMin   = 180;
$safeVoltageMax   = 240;
$safeVoltage      = ($discoVoltage >= $safeVoltageMin && $discoVoltage <= $safeVoltageMax);

// Battery & solar levels (percentage)
$batteryLevel     = rand(0, 100); // Inverter battery level
$solarOutput      = rand(0, 100); // Solar panel output level

// Generator status
$generatorOn      = false;
$generatorReason  = "";

// ─── CHANGEOVER LOGIC ────────────────────────────────────────────────────────

$currentSource   = "";
$changeoverNote  = "";

// PRIORITY 1 — DISCO is default if available and voltage is safe
if ($discoAvailable == 1 && $safeVoltage) {
    $currentSource  = "DISCO/NEPA";
    $changeoverNote = "DISCO supply is stable (Voltage: {$discoVoltage}V). Running on DISCO.";

// PRIORITY 2 — DISCO available but voltage is unsafe → switch to inverter or solar
} elseif ($discoAvailable == 1 && !$safeVoltage) {
    $changeoverNote = "DISCO voltage is unsafe ({$discoVoltage}V). Switching away from DISCO.";

    if ($solarOutput > 18 && $sunlight == 1) {
        $currentSource  = "Solar Power";
        $changeoverNote .= " Switched to Solar Power (Output: {$solarOutput}%).";
    } elseif ($batteryLevel > 18) {
        $currentSource  = "Inverter Battery";
        $changeoverNote .= " Switched to Inverter Battery (Level: {$batteryLevel}%).";
    } elseif ($generatorFuel == 1) {
        $currentSource  = "Generator";
        $generatorOn    = true;
        $generatorReason = "DISCO voltage unsafe & solar/inverter too low.";
        $changeoverNote .= " Generator activated — solar and inverter levels too low.";
    } else {
        $currentSource  = "No Power Source Available";
        $changeoverNote .= " No fallback available. Generator has no fuel.";
    }

// PRIORITY 3 — No DISCO → try solar first
} elseif ($sunlight == 1 && $solarOutput > 18) {
    $currentSource  = "Solar Power";
    $changeoverNote = "No DISCO supply. Running on Solar Power (Output: {$solarOutput}%).";

// PRIORITY 4 — No solar → try inverter
} elseif ($batteryLevel > 18) {
    $currentSource  = "Inverter Battery";
    $changeoverNote = "No DISCO or Solar. Running on Inverter Battery (Level: {$batteryLevel}%).";

// PRIORITY 5 — Solar/inverter below 18% → switch to generator
} elseif ($generatorFuel == 1) {
    $currentSource   = "Generator";
    $generatorOn     = true;
    $generatorReason = "Solar output ({$solarOutput}%) and inverter level ({$batteryLevel}%) are both below 18%.";
    $changeoverNote  = "Solar and Inverter too low. Generator activated.";

// PRIORITY 6 — Nothing available
} else {
    $currentSource  = "No Power Source Available";
    $changeoverNote = "All power sources unavailable or too low. No fuel for generator.";
}

// ─── CHARGING STATUS ─────────────────────────────────────────────────────────

$chargingStatus = "";

if ($batteryLevel < 100) {
    if ($currentSource == "DISCO/NEPA" || ($discoAvailable == 1 && $safeVoltage)) {
        $chargingStatus = "Charging inverter with DISCO/NEPA";
    } elseif ($sunlight == 1) {
        $chargingStatus = "Charging inverter with Solar";
    } elseif ($generatorOn) {
        $chargingStatus = "Charging inverter with Generator";
    } else {
        $chargingStatus = "No charging source available";
    }
} else {
    $chargingStatus = "Inverter is fully charged";
}

// ─── COST CALCULATION ────────────────────────────────────────────────────────

$discoCostPerHour     = 200; // ₦ per hour
$solarCostPerHour     = 20;
$inverterCostPerHour  = 40;
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

// ─── SUMMARY ARRAY ───────────────────────────────────────────────────────────

$summary = [
    'customer'        => $name,
    'band'            => $band,
    'source'          => $currentSource,
    'changeover_note' => $changeoverNote,
    'charging'        => $chargingStatus,
    'battery_level'   => $batteryLevel . '%',
    'solar_output'    => $solarOutput . '%',
    'disco_voltage'   => $discoVoltage . 'V',
    'generator_on'    => $generatorOn ? "Yes — " . $generatorReason : "No",
    'cost_per_hour'   => '₦' . $currentCost,
    'savings'         => '₦' . $savings,
];
?>