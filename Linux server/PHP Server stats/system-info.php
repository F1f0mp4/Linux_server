<?php
header('Content-Type: text/plain');

// Function to fetch CPU name and frequency
function getCpuInfo() {
    $cpuInfo = trim(shell_exec("lscpu | grep 'Model name' | awk -F: '{print $2}'"));
    $frequency = trim(shell_exec("cat /proc/cpuinfo | grep 'cpu MHz' | awk -F: '{print $2}' | head -n 1"));
    $frequency = $frequency ? intval($frequency) : "N/A";

    // Convert frequency to GHz if over 1000 MHz
    if ($frequency !== "N/A" && $frequency > 1000) {
        $frequency = number_format($frequency / 1000, 2) . "GHz";
    } else {
        $frequency .= "MHz";
    }

    // Simplify CPU name
    if (preg_match('/i[3579]-\d{4}/', $cpuInfo, $matches)) {
        $cpuName = $matches[0];
    } else {
        $cpuName = "Unknown CPU";
    }

    return $cpuName . "  " . $frequency;
}

// Function to fetch overall CPU usage
function getCpuUsage() {
    // Get CPU usage with top command
    $cpuUsage = shell_exec("top -bn1 | grep 'Cpu(s)' | sed 's/.*, *\([0-9.]*\)%* id.*/\1/' | awk '{print 100 - $1}'");
    return number_format(floatval($cpuUsage), 2) . "%";
}

// Function to fetch overall CPU temperature
function getCpuTemperature() {
    $temperature = shell_exec("sensors | grep 'Package id 0' | awk '{print $4}'");
    // Clean up the temperature format
    $temperature = trim($temperature);
    $temperature = preg_replace('/\+/', '', $temperature); // Remove the '+' sign if it exists
    return $temperature ? intval($temperature) . "°C" : "N/A"; // Cast to int and append °C
}

// Function to fetch per-core usage and temperature
function getCoreDetails() {
    $numCores = intval(trim(shell_exec("lscpu | grep '^Core(s) per socket:' | awk '{print $4}'")));
    if ($numCores === 0) {
        return "No cores detected.\n";
    }

    // Get per-core usage with mpstat
    $mpstatOutput = shell_exec("mpstat -P ALL 1 1");
    $mpstatLines = explode("\n", $mpstatOutput);

    // Skip header and process per-core data
    $coreUsages = [];
    foreach ($mpstatLines as $line) {
        // Look for lines with per-core data
        if (preg_match('/^\s*[0-9]+\s+/', $line)) {
            $columns = preg_split('/\s+/', trim($line));
            // Assuming columns[10] is %idle, calculate %usage as 100 - %idle
            if (isset($columns[10])) { 
                $coreUsages[] = 100 - floatval($columns[10]); // Correct index for %idle
            }
        }
    }

    // Get per-core temperatures
    $coreTemps = explode("\n", trim(shell_exec("sensors | grep 'Core' | awk '{print $3}'")));

    // Prepare details
    $details = "";
    for ($i = 0; $i < $numCores; $i++) {
        // Ensure we get valid usage and temperature data
        $usage = isset($coreUsages[$i]) ? number_format($coreUsages[$i], 2) : "N/A";
        $temp = isset($coreTemps[$i]) ? intval($coreTemps[$i]) . "°C" : "N/A"; // Cast to int and append °C
        $details .= "Core " . $i . ": Usage  " . $usage . "%, " . $temp . "\n";
    }

    return $details;
}

// Function to fetch disk usage for each mounted device
function getDiskDetails() {
    // Get disk usage for all mounted devices using df command
    $diskUsageOutput = shell_exec("df -h --output=source,used,avail,pcent | tail -n +2"); 
    $lines = explode("\n", trim($diskUsageOutput));
    
    $diskDetails = "Disk Usage:\n";
    
    // Iterate through each line (skip the header)
    foreach ($lines as $line) {
        $columns = preg_split('/\s+/', $line);
        if (isset($columns[0]) && isset($columns[1]) && isset($columns[2]) && isset($columns[3])) {
            // Check if the disk is of type /dev/sdb* (could be sdb1, sdb2, etc.)
            if (preg_match('/^\/dev\/sdb/', $columns[0])) {
                // Extract disk name and usage details
                $diskDetails .= $columns[0] . " Used: " . $columns[1] . " | Available: " . $columns[2] . " | Usage: " . $columns[3] . "\n";
            }
        }
    }

    return $diskDetails;
}

// Output the data
echo "Falcon server stats\n\n";
echo "Real-Time CPU Monitor\n\n";
echo getCpuInfo() . "\n";
echo "\nCPU Usage: " . getCpuUsage() .", " . getCpuTemperature() . "\n";
echo "\nCore Details:\n";
echo getCoreDetails();
echo "\n";
echo getDiskDetails();
?>
